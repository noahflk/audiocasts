<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use App\Models\File;

class FileDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Audiobook $audiobook, File $file)
    {
        return response()->file($file->path);
    }
}
