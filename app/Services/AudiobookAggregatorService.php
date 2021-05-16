<?php

namespace App\Services;

use App\Facades\Pathlib;
use App\Models\CoverImage;
use App\Models\Media;
use Illuminate\Support\Str;
use getID3;

class AudiobookAggregatorService
{
    private $getID3;
    private UtilService $utilService;

    // takes an audiobook
    // method to start a scan to get the metadata on the audiobook
    // return said metadata

    public function __construct(
        getID3 $getID3,
        UtilService $audiobookUtilService,
    )
    {
        $this->getID3 = $getID3;
        $this->utilService = $audiobookUtilService;
    }

    public function get(): array
    {
        $directories = $this->getAudiobookDirectories();
        return $this->getAudiobooksMetadata($directories);
    }

    private function getAudiobookDirectories(): array
    {
        $directories = [];
        $topLevelDirectories = Media::all()->pluck("path");

        foreach ($topLevelDirectories as $directory) {
            $directories = array_merge($directories, Pathlib::allDirectories($directory));
        }

        // Filter so only audiobook directories are considered
        return array_filter($directories, function ($directory) use ($directories) {
            return $this->directoryIsAudiobbok($directory, $directories);
        });
    }


    private function getAudiobooksMetadata($directories): array
    {
        return array_map(function ($directory) {
            if (empty($this->utilService->getAudioFilesInDirectory($directory))) {
                return;
            }

            // Take the first file in the directory
            $file = $this->utilService->getAudioFilesInDirectory($directory)[0];

            $metadata = $this->getID3->analyze($file);
            $this->getID3->CopyTagsToComments($metadata);

            $data = [
                'author' => 'Unknown author',
                'directory' => $directory,
                // Manually add timestamps since bulk inserts don't add them automatically
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];


            if (isset($metadata['comments']['picture'][0]['data'])) {
                $data['cover'] = CoverImage::save($metadata['comments']['picture'][0]['data']);
            }

            if (isset($metadata['comments']['album'][0])) {
                $album = $metadata['comments']['album'][0];

            } else {
                $exploded = explode('/', $directory);
                $album = end($exploded);

            }

            $data['title'] = $album;
            $data['slug'] = $album;

            if (isset($metadata['comments']['artist'][0])) {
                $data['author'] = $metadata['comments']['artist'][0];
            }

            return $data;
        }, $directories);
    }

    private function directoryIsAudiobbok($directoryToCheck, $directories): bool
    {
        // Only final directories that contain no more subdirectories are considered to be audiobooks
        foreach ($directories as $directory) {
            if (Str::startsWith($directory, $directoryToCheck) && $directory != $directoryToCheck) {
                return false;
            }
        }

        if (empty($this->utilService->getAudioFilesInDirectory($directoryToCheck))) {
            return false;
        }

        return true;
    }
}
