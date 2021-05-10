<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;
use Illuminate\Database\DatabaseManager;
use Jackiedo\DotenvEditor\DotenvEditor;
use Throwable;

class InitCommand extends Command
{
    private const NON_INTERACTION_MAX_ATTEMPT_COUNT = 10;

    protected $signature = 'audiocasts:init';
    protected $description = 'Initializes Audiocasts.';

    private $artisan;
    private $db;
    private $dotenvEditor;

    public function __construct(
        Artisan $artisan,
        DatabaseManager $db,
        DotenvEditor $dotenvEditor,
    )
    {
        parent::__construct();

        $this->artisan = $artisan;
        $this->db = $db;
        $this->dotenvEditor = $dotenvEditor;
    }

    public function handle(): void
    {
        $this->comment('Starting Audiocasts setup');

        if ($this->isNonInteractive()) {
            $this->info('Running in no-interaction mode');
        }

        try {
            $this->maybeSetUpDatabase();
            $this->migrateDatabase();
            $this->maybeGenerateAppKey();
        } catch (Throwable $error) {
            $this->error("Audiocasts installation didn't finish successfully. Sorry for this!");
            return;
        }

        $this->comment(PHP_EOL . 'Success! Audiocasts is now ready ✅');
    }

    private function maybeGenerateAppKey(): void
    {
        if (!config('app.key')) {
            $this->info('Generating app key');
            $this->artisan->call('key:generate');
        } else {
            $this->comment('App key exists, will be skipped');
        }
    }

    private function maybeSetUpDatabase(): void
    {
        $attemptCount = 0;

        while (true) {
            // In non-interactive mode, we must not endlessly attempt to connect.
            // Doing so will just end up with a huge amount of "failed to connect" logs.
            // We do retry a little, though, just in case there's some kind of temporary failure.
            if ($this->isNonInteractive() && $attemptCount >= self::NON_INTERACTION_MAX_ATTEMPT_COUNT) {
                $this->warn('Maximum database connection attempts reached. Giving up.');
                break;
            }

            $attemptCount++;

            try {
                // Make sure the config cache is cleared before another attempt.
                $this->artisan->call('config:clear');
                $this->db->reconnect()->getPdo();

                break;
            } catch (Throwable $error) {
                $this->error($error->getMessage());


                // We only try to update credentials if running in interactive mode.
                // Otherwise, we require admin intervention to fix them.
                // This avoids inadvertently wiping credentials if there's a connection failure.
                if ($this->isNonInteractive()) {
                    $warning = sprintf(
                        '%sUnable to connect to the database. Attempt: %d/%d',
                        PHP_EOL,
                        $attemptCount,
                        self::NON_INTERACTION_MAX_ATTEMPT_COUNT
                    );

                    $this->warn($warning);
                } else {
                    $this->warn('Unable to connect to the database. Starting setup.');
                    $this->setDatabaseConfig();
                }
            }
        }
    }

    private function setDatabaseConfig(): void
    {
        $config = [
            'DB_CONNECTION' => '',
            'DB_HOST' => '',
            'DB_PORT' => '',
            'DB_DATABASE' => '',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
        ];

        $config['DB_CONNECTION'] = $this->choice(
            'Your DB driver of choice',
            [
                'mysql' => 'MySQL/MariaDB',
                'pgsql' => 'PostgreSQL',
                'sqlsrv' => 'SQL Server',
                'sqlite' => 'SQLite',
            ],
            'sqlite'
        );

        if ($config['DB_CONNECTION'] === 'sqlite') {
            $path = $this->ask("Absolute path to the DB file (will be created if it doesn't exist");
            $this->maybeGenerateDatabaseFile($path);

            $config['DB_DATABASE'] = $path;
        } else {
            $config['DB_HOST'] = $this->anticipate('DB host', ['127.0.0.1', 'localhost']);
            $config['DB_PORT'] = (string)$this->ask('DB port (leave empty for default)');
            $config['DB_DATABASE'] = $this->anticipate('DB name', ['audiocasts']);
            $config['DB_USERNAME'] = $this->anticipate('DB user', ['audiocasts']);
            $config['DB_PASSWORD'] = (string)$this->ask('DB password');
        }

        foreach ($config as $key => $value) {
            $this->dotenvEditor->setKey($key, $value);
        }

        $this->dotenvEditor->save();

        // Set the config so that the next DB attempt uses refreshed credentials
        config([
            'database.default' => $config['DB_CONNECTION'],
            "database.connections.{$config['DB_CONNECTION']}.host" => $config['DB_HOST'],
            "database.connections.{$config['DB_CONNECTION']}.port" => $config['DB_PORT'],
            "database.connections.{$config['DB_CONNECTION']}.database" => $config['DB_DATABASE'],
            "database.connections.{$config['DB_CONNECTION']}.username" => $config['DB_USERNAME'],
            "database.connections.{$config['DB_CONNECTION']}.password" => $config['DB_PASSWORD'],
        ]);
    }

    private function migrateDatabase(): void
    {
        $this->info('Migrating database');
        $this->artisan->call('migrate', ['--force' => true]);
    }

    private function maybeGenerateDatabaseFile($path): void
    {
        try {
            if (file_exists($path)) {
                $this->comment('SQLite file already exists: ' . $path);
                return;
            }

            file_put_contents($path, '');
            $this->comment('Created empty SQLite file at: ' . $path);
        } catch (Throwable $error) {
            $this->error('Unable to create SQLite file ' . $path);
        }

    }

    private function isNonInteractive(): bool
    {
        return (bool)$this->option('no-interaction');
    }
}
