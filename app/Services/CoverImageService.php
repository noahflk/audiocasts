<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\CoverImage;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use getID3;

class CoverImageService
{
    private $coversFromDatabase;
    private $coversFromDisk;

    private $getID3;
    private $utilSerivce;

    public function __construct(
        getID3 $getID3,
        UtilService $utilService,
    )
    {
        $this->getID3 = $getID3;
        $this->utilSerivce = $utilService;
    }

    public function cleanup(): array
    {
        // Fetch all audiobook cover IDs
        $this->coversFromDatabase = array_merge(
            Audiobook::getAllCovers(),
            File::getAllCovers(),
        );

        // Fetch all covers on disk
        $this->coversFromDisk = Storage::disk('public')->files(config('audiocasts.cover_directory'));
        $deletedCovers = $this->deleteUnusedCovers();
        $regeneratedCovers = $this->createMissingCovers();

        return [
            'regenerated_covers' => count($regeneratedCovers),
            'deleted_covers' => count($deletedCovers)
        ];
    }

    private function deleteUnusedCovers(): array
    {
        $deletedCovers = [];

        // Go through all covers and delete the one that are not part of $audiobookCovers
        foreach ($this->coversFromDisk as $coverPath) {
            // Check if cover belongs to audiobook
            // Also check if cover belongs to file
            $coverName = substr($coverPath, strlen(config('audiocasts.cover_directory')));
            $foundInDatabase = in_array($coverName, $this->coversFromDatabase);

            if (!$foundInDatabase && $this->utilSerivce->isCoverFile($coverPath)) {
                Storage::disk('public')->delete($coverPath);
                array_push($deletedCovers, $coverPath);
            }
        }

        return $deletedCovers;
    }

    private function createMissingCovers(): array
    {
        $missingCovers = $this->getMissingCovers();

        $regeneratedAudiobookCovers = $this->generateMissingAudiobookCovers($missingCovers);
        $regeneratedAudioFileCovers = $this->generateMissingFileCovers($missingCovers);

        return array_merge($regeneratedAudiobookCovers, $regeneratedAudioFileCovers);
    }

    private function getMissingCovers(): array
    {
        $missingAudiobookCovers = [];

        // Check if cover from Disk isn't found in Audiobook table AND AudioFile table
        // Make sure each cover from Audiobook table AND AudioFile table exists on disk
        foreach ($this->coversFromDatabase as $cover) {
            $coverPath = config('audiocasts.cover_directory') . $cover;

            if (!in_array($coverPath, $this->coversFromDisk)) {
                array_push($missingAudiobookCovers, $cover);
            }
        }

        return $missingAudiobookCovers;
    }

    private function generateMissingAudiobookCovers($missingCovers)
    {
        $missingAudiobooks = Audiobook::whereIn('cover', $missingCovers)->get();

        $regeneratedCovers = [];

        // Generate cover from first file of Audiobook
        foreach ($missingAudiobooks as $missingAudiobook) {
            // Take the first file in the directory
            $file = $this->utilSerivce->getAudioFilesInDirectory($missingAudiobook->directory)[0];
            $metadata = $this->getID3->analyze($file);
            $this->getID3->CopyTagsToComments($metadata);

            $missingAudiobook->cover = CoverImage::save($metadata['id3v2']['APIC'][0]['data']);
            $missingAudiobook->save();
            array_push($regeneratedCovers, $missingAudiobook->cover);
        }


        return $regeneratedCovers;
    }

    public function generateMissingFileCovers($missingCovers): array
    {
        $regeneratedCovers = [];

        $missingFiles = File::whereIn('cover', $missingCovers)->get();

        // Generate cover from file path
        foreach ($missingFiles as $missingAudioFile) {
            // Take the first file in the directory
            $metadata = $this->getID3->analyze($missingAudioFile->path);
            $this->getID3->CopyTagsToComments($metadata);
            $missingAudioFile->cover = CoverImage::save($metadata['id3v2']['APIC'][0]['data']);
            $missingAudioFile->save();
            array_push($regeneratedCovers, $missingAudioFile->cover);
        }

        return $regeneratedCovers;
    }
}
