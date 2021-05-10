<?php

namespace App\Services;

use App\Facades\Pathlib;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UtilService
{
    const VALID_FILE_TYPES = ['.mp3', '.MP3'];
    const COVER_DIRECTORY = 'public/covers/';

    public function coversAreIdentical($cover, $coverFilename)
    {
        $encodedAudiobookCover = base64_encode(Storage::get(self::COVER_DIRECTORY . $coverFilename));

        return $encodedAudiobookCover == $cover;
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
        $filenameWithoutExtension = substr($filename, 0, count($coverFiletype));
        return Str::isUuid() && str_ends_with($filename, $filenameWithoutExtension);
    }
}
