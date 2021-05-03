<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\CoverImage;
use App\Models\File;
use App\Repositories\FileRepository;
use SplFileInfo;
use getID3;

class AudiobookSynchronizerService
{
    public const SYNC_RESULT_SUCCESS = 1;
    public const SYNC_RESULT_BAD_FILE = 2;
    public const SYNC_RESULT_UNMODIFIED = 3;

    private $fileRepository;
    private $utilService;
    private $getID3;


    private $audiobook;
    private $files;


    public function __construct(
        UtilService $utilService,
        FileRepository $fileRepository,
        getID3 $getID3,
    )
    {
        $this->utilService = $utilService;
        $this->fileRepository = $fileRepository;
        $this->getID3 = $getID3;
    }

    public function sync($audiobook): int
    {
        $this->audiobook = $audiobook;

        // store OR update audiobook metadata
        // TODO: could be done better since currently we do not check if changed occurred and metadata is based on single file
        $hash = $this->utilService->getPathHash($audiobook->directory);
        Audiobook::updateOrCreate(["id" => $hash], $audiobook);

        // get all audiobook files
        $this->files = $this->utilService->getAudioFilesInDirectory($this->audiobook->path);

        foreach ($this->files as $file) {
            $this->syncFile($file);
        }
    }

    private function syncFile($file): int
    {
        // if file is not new or changed
        if (!$this->isFileNewOrChanged($file)) {
            return self::SYNC_RESULT_UNMODIFIED;
        }

        // get file metadata
        $metadata = $this->getFileMetadata($file);

        if (!$metadata) {
            return self::SYNC_RESULT_BAD_FILE;
        }

        $hash = $this->utilService->getPathHash($file);
        File::updateOrCreate(["id" => $hash], $metadata);

        return self::SYNC_RESULT_SUCCESS;
    }

    private function getFileMetadata($file): object
    {
        $metadata = $this->getID3->analyze($file);
        $this->getID3->CopyTagsToComments($metadata);

        $info = new SplFileInfo($file);

        $data = [
            "path" => $file,
            "name" => null,
            "cover" => null,
            "audiobook_id" => $this->audiobook->id,
            "type" => $metadata["mime_type"],
            "filesize" => $metadata["filesize"],
            "playtime" => $metadata["playtime_seconds"],
            "bitrate" => round($metadata["audio"]["bitrate"]),
            "mtime" => $info->getMTime(),
            // Manually add timestamps since bulk inserts don't add them automatically
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s')
        ];

        if (isset($metadata["comments"]["title"][0])) {
            $data["name"] = $metadata["comments"]["title"][0];
        } else {
            $data["name"] = $metadata["filename"];
        }

        if (isset($metadata["comments"]["picture"][0]["data"])) {
            $data["cover"] = $this->getDeduplicatedCoverPath($metadata["comments"]["picture"][0]["data"]);
        }

        return $data;
    }

    private function getDeduplicatedCoverPath($cover): ?string
    {
        // If cover is same as audiobook cover, no action is necessary
        if ($this->utilService->coversAreIdentical($cover, $this->audiobook->cover)) {
            return null;
        }

        // Check if cover is identical to other file of same audiobook
        foreach ($this->files as $file) {
            if ($this->utilService->coversAreIdentical($file->cover, $cover)) {
                return $file->cover;
            }
        }

        // If no identical match is found, store it as a new cover
        return CoverImage::save($cover);
    }

    private function isFileNewOrChanged($file): bool
    {
        return $this->isFileNew($file) || $this->isFileChanged($file);
    }

    private function isFileNew($filePath): bool
    {
        return !$this->fileRepository->getOneById($this->utilService->getPathHash($filePath));
    }

    private function isFileChanged($filePath): bool
    {
        $info = new SplFileInfo($filePath);
        $fileModifiedTime = $info->getMTime();
        $file = $this->fileRepository->getOneById($this->utilService->getPathHash($filePath));
        return !$this->isFileNew($filePath) && $file->mtime !== $fileModifiedTime;
    }
}
