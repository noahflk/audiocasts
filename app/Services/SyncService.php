<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Console\Commands\ScanCommand;

class SyncService
{
    private $audiobookAggregatorService;
    private $audiobookSyncService;
    private $coverImageSerivce;
    private $utilService;

    public function __construct(
        AudiobookAggregatorService $audiobookAggregatorService,
        AudiobookSyncService $audiobookSyncService,
        CoverImageService $coverImageSerivce,
        UtilService $utilService,
    )
    {
        $this->audiobookAggregatorService = $audiobookAggregatorService;
        $this->audiobookSyncService = $audiobookSyncService;
        $this->coverImageSerivce = $coverImageSerivce;
        $this->utilService = $utilService;
    }

    public function scan(?ScanCommand $scanCommand = null)
    {
        $results = [
            'success' => [],
            'bad_files' => [],
            'unmodified' => [],
        ];

        // Get all audiobook directories, no matter if they exist or not
        $audiobooks = $this->audiobookAggregatorService->get();

        if ($scanCommand) {
            $scanCommand->createProgressBar(count($audiobooks));
        }

        // now go through each audiobook
        foreach ($audiobooks as $audiobook) {
            $result = $this->audiobookSyncService->sync($audiobook);

            switch ($result) {
                case AudiobookSyncService::SYNC_RESULT_SUCCESS:
                    $results['success'][] = $audiobook->directory;
                    break;

                case AudiobookSyncService::SYNC_RESULT_UNMODIFIED:
                    $results['unmodified'][] = $audiobook->directory;
                    break;

                default:
                    $results['bad_files'][] = $audiobook->directory;
                    break;
            }

            if ($scanCommand) {
                $scanCommand->advanceProgressBar();
                // TODO: also send error that happened in audiobookSyncService back
                $scanCommand->logSyncStatusToConsole($audiobook->directory, $result);
            }
        }

        // Delete audiobooks that no longer exist
        $hashes = array_map(function (string $path): string {
            return $this->utilService->getPathHash($path);
        }, array_merge($results['unmodified'], $results['success']));

        Audiobook::deleteWhereIDsNotIn($hashes);

        $this->coverImageSerivce->cleanup();
    }
}
