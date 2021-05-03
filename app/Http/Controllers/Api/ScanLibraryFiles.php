<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;

class ScanLibraryFiles extends Controller
{
    public function __invoke(SyncService $audiobookFileService)
    {
        return response()->json(
            array_merge($audiobookFileService->scanAudiobookFiles(), [
            "duration" => microtime(true) - LARAVEL_START
        ]));
    }
}
