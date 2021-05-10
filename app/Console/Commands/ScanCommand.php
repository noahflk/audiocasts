<?php

namespace App\Console\Commands;

use App\Services\FileSynchronizer;
use App\Services\SyncService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ScanCommand extends Command
{
    protected $signature = 'audiocasts:scan';
    protected $description = 'Scans media directories for audiobooks.';

    private $synced = 0;
    private $ignored = 0;
    private $invalid = 0;
    private $syncService;
    private $progressBar;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SyncService $syncService)
    {
        parent::__construct();

        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting media scan' . PHP_EOL);

        $this->syncService->scan($this);

        $this->output->writeln(
            PHP_EOL . PHP_EOL
            . "<info>Completed! {$this->synced} new or updated audiobook(s)</info>, "
            . "{$this->ignored} unchanged audiobook(s), "
            . "and <comment>{$this->invalid} invalid file(s)</comment>."
        );
    }

    public function logSyncStatusToConsole(string $path, int $result, ?string $reason = null): void
    {
        $name = basename($path);

        if ($result === FileSynchronizer::SYNC_RESULT_UNMODIFIED) {
            ++$this->ignored;
        } elseif ($result === FileSynchronizer::SYNC_RESULT_BAD_FILE) {
            if ($this->option('verbose')) {
                $this->error(PHP_EOL . "'$name' is not a valid media file: " . $reason);
            }

            ++$this->invalid;
        } else {
            ++$this->synced;
        }
    }

    public function createProgressBar(int $max): void
    {
        $this->progressBar = $this->getOutput()->createProgressBar($max);
    }

    public function advanceProgressBar(): void
    {
        $this->progressBar->advance();
    }
}
