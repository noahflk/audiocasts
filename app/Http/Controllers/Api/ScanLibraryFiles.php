<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\Response;

class ScanLibraryFiles extends Controller
{
    public function __construct(
        private SyncService $syncService
    )
    {
    }

    public function __invoke(SyncService $audiobookFileService)
    {
        $this->syncService->scan();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
