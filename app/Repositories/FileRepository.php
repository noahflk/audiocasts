<?php

namespace App\Repositories;

use App\Models\File;
use App\Services\UtilService;

class FileRepository extends AbstractRepository
{
    private $utilService;

    public function __construct(UtilService $utilService)
    {
        parent::__construct();

        $this->utilService = $utilService;
    }

    public function getOneByPath(string $path): ?File
    {
        return $this->getOneById($this->utilService->getPathHash($path));
    }
}
