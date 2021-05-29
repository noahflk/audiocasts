<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class LocalDiskCoverImageService
{
    public function getExistingCoverFileOnDiskIfAvailable(string $directory): ?string
    {
        // Find cover file on disk and, if it exists, copy it to the covers directory and return filename

        $pngPath = $directory . '/cover.png';

        if (file_exists($pngPath)) {
            return $this->storeFileFromDiskAsCover($pngPath, 'png');
        }

        $jpgPath = $directory . '/cover.jpg';

        if (file_exists($jpgPath)) {
            return $this->storeFileFromDiskAsCover($jpgPath, 'jpg');
        }

        return null;
    }

    private function storeFileFromDiskAsCover(string $filePath, string $extension): string
    {
        $imageName = Str::uuid() . '.' . $extension;
        Storage::disk('public')->putFileAs(config('audiocasts.cover_directory'), new File($filePath), $imageName);

        return $imageName;
    }
}
