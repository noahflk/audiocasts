<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\CoverImage;
use App\Models\File;
use App\Repositories\FileRepository;
use SplFileInfo;
use getID3;

class AudiobookSyncService
{
    public const SYNC_RESULT_SUCCESS = 1;
    public const SYNC_RESULT_BAD_FILE = 2;
    public const SYNC_RESULT_UNMODIFIED = 3;

    private $utilService;
    private $getID3;
    private $fileRepository;


    private $audiobook;
    private $files = [];


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
        // store OR update audiobook metadata
        // TODO: could be done better since currently we do not check if changed occurred and metadata is based on single file
        $hash = $this->utilService->getPathHash($audiobook['directory']);
        $this->audiobook = Audiobook::updateOrCreate(['id' => $hash], $audiobook);

        // get all audiobook files
        $filePaths = $this->utilService->getAudioFilesInDirectory($this->audiobook['directory']);

        $audiobookResult = self::SYNC_RESULT_UNMODIFIED;

        // TODO: This result should not be dependent on a single file
        foreach ($filePaths as $file) {
            $result = $this->syncFile($file);

            if ($result === self::SYNC_RESULT_BAD_FILE || $result === self::SYNC_RESULT_SUCCESS) {
                $audiobookResult = $result;
            }
        }

        return $audiobookResult;
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
        File::updateOrCreate(['id' => $hash], $metadata);

        return self::SYNC_RESULT_SUCCESS;
    }

    private function getFileMetadata($file): array
    {
        $metadata = $this->getID3->analyze($file);
        $this->getID3->CopyTagsToComments($metadata);

        $info = new SplFileInfo($file);

        // TODO: Handle failure with getID3

        $data = [
            'path' => $file,
            'name' => null,
            'cover' => null,
            'audiobook_id' => $this->audiobook->id,
            'type' => $metadata['mime_type'],
            'filesize' => $metadata['filesize'],
            'playtime' => $metadata['playtime_seconds'],
            'bitrate' => round($metadata['audio']['bitrate']),
            'mtime' => $info->getMTime(),
            // Manually add timestamps since bulk inserts don't add them automatically
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (isset($metadata['comments']['title'][0])) {
            $data['name'] = $metadata['comments']['title'][0];
        } else {
            $data['name'] = $metadata['filename'];
        }

        if (isset($metadata['comments']['picture'][0]['data'])) {
            $data['cover'] = $this->getDeduplicatedCoverPath($metadata['comments']['picture'][0]['data']);
        }

        $this->files[] = $data;
        return $data;
    }

    /**
     * When the cover of a file is the same as the audiobook cover, there is no need to store it again
     * The same applies when another file of the same audiobook already has the same cover
     * Instead, the property in the DB is left blank which implies the cover from the audiobook should be used
     *
     * @param string $cover
     * @return string|null
     */
    private function getDeduplicatedCoverPath(string $cover): ?string
    {
        // If cover is same as audiobook cover, no action is necessary
        if ($this->utilService->coversAreIdentical($cover, $this->audiobook->cover)) {
            return null;
        }

        // Check if cover is identical to other file of same audiobook
        foreach ($this->files as $file) {
            if ($file['cover'] && $this->utilService->coversAreIdentical($cover, $file['cover'])) {
                return $file['cover'];
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
        return !File::where('id', $this->utilService->getPathHash($filePath))->exists();
    }

    private function isFileChanged($filePath): bool
    {
        $info = new SplFileInfo($filePath);
        $fileModifiedTime = $info->getMTime();
        $file = $this->fileRepository->getOneByPath($filePath);

        // If file is not found, we assume it has changed since we cannot know if that's not the case
        // But currently that's an edge-case which shouldn't occur
        if (!$file) {
            return true;
        }

        // The PHP PDO casts integers from the DB to strings, so we need to ensure both values are of the same datatype
        return !$this->isFileNew($filePath) && $file->mtime !== strval($fileModifiedTime);
    }
}
