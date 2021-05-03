<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\FileSynchronizer;
use App\Services\SyncService;
use Illuminate\Console\Command;

class ScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiocasts:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $synced = 0;
    private $ignored = 0;
    private $invalid = 0;
    private $syncService;


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
        $this->info('Starting media scan'. PHP_EOL);

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
}
