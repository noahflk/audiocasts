<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\CoverImage;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use getID3;
use Illuminate\Support\Str;

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
            Audiobook::getUniqueCovers(),
            File::getUniqueCovers(),
        );

        // Fetch all covers on disk
        $this->coversFromDisk = $this->getCoversFromDisk();

        $deletedCovers = $this->deleteUnusedCovers();
        $regeneratedCovers = $this->createMissingCovers();

        return [
            'regenerated_covers' => count($regeneratedCovers),
            'deleted_covers' => count($deletedCovers)
        ];
    }

    public function generateMissingFileCovers($missingCovers): array
    {
        // TODO: A replaced audiobook covers won't get written to disk fast enough, thus getting picked up again here
        $regeneratedCovers = [];

        $uniqueFilesWithMissingCover = File::whereIn('cover', $missingCovers)->get()->unique('cover');

        // Generate cover from file path
        foreach ($uniqueFilesWithMissingCover as $fileWithMissingCover) {
            // Take the first file in the directory
            $metadata = $this->getID3->analyze($fileWithMissingCover->path);
            $this->getID3->CopyTagsToComments($metadata);
            $newCoverFilename = CoverImage::save($metadata['id3v2']['APIC'][0]['data']);
            File::where('cover', $fileWithMissingCover->cover)->update(['cover' => $newCoverFilename]);

            array_push($regeneratedCovers, $newCoverFilename);
        }

        return $regeneratedCovers;
    }

    private function deleteUnusedCovers(): array
    {
        $deletedCovers = [];

        // Go through all covers and delete the one that are not part of $audiobookCovers
        foreach ($this->coversFromDisk as $coverName) {
            // Check if cover belongs to audiobook
            // Also check if cover belongs to file
            $coverPath = config('audiocasts.cover_directory') . $coverName;
            $foundInDatabase = in_array($coverName, $this->coversFromDatabase);

            // We only delete files that could be actual cover files. Meaning they have a UUID filename and are JPGs
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

        return array_merge($this->generateMissingAudiobookCovers($missingCovers), $this->generateMissingFileCovers($missingCovers));
    }

    private function getMissingCovers(): array
    {
        $missingAudiobookCovers = [];

        // Check if cover from Disk isn't found in Audiobook table AND AudioFile table
        // Make sure each cover from Audiobook table AND AudioFile table exists on disk
        foreach ($this->coversFromDatabase as $cover) {
            if (!in_array($cover, $this->coversFromDisk)) {
                array_push($missingAudiobookCovers, $cover);
            }
        }

        return $missingAudiobookCovers;
    }

    private function generateMissingAudiobookCovers($missingCovers): array
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

    private function getCoversFromDisk(): array
    {
        $files = Storage::disk('public')->files(config('audiocasts.cover_directory'));

        $filteredFiles = array_values(array_filter($files, function ($file) {
            return Str::endsWith($file, '.jpg');
        }));

        return array_map(function ($file) {
            return basename($file);
        }, $filteredFiles);
    }
}
