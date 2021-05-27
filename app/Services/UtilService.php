<?php

namespace App\Services;

use App\Facades\Pathlib;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UtilService
{
    const VALID_FILE_TYPES = ['.mp3', '.MP3'];

    /**
     * Checks if a Base64 encoded image is equal to a image stored on disk
     *
     * @param ?string $cover Base64 encoded data of the cover image
     * @param ?string $coverPath Path to a cover already stored on disk
     * @return bool
     */
    public function coversAreIdentical(?string $cover, ?string $coverPath): bool
    {
        // If either the cover or the path to the cover are not provided, they cannot be identical
        if (!$cover || !$coverPath) {
            return false;
        }

        // TODO: Handle file not found
        $coverFromPath = Storage::disk('public')->get(config('audiocasts.cover_directory') . $coverPath);

        return base64_encode($cover) == base64_encode($coverFromPath);
    }


    public function getAudioFilesInDirectory($directory): array
    {
        $files = Pathlib::files($directory);

        return array_values(array_filter($files, function ($file) {
            return Str::endsWith($file, self::VALID_FILE_TYPES);
        }));
    }

    // Get unique hash for file based on its path
    // Dependant on current application key, so that hash changes for new setup
    public function getPathHash(string $path): string
    {
        return md5(config('app.key') . $path);
    }

    public function isCoverFile(string $filename): bool
    {
        $coverFiletype = config('audiocasts.cover_file_type');

        if (!str_ends_with($filename, $coverFiletype)) {
            return false;
        }

        $filenameWithoutExtension = substr($filename, 0, -strlen('.' . $coverFiletype));

        return Str::isUuid($filenameWithoutExtension);
    }
}
