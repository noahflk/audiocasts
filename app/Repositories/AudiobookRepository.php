<?php

namespace App\Repositories;

use App\Models\Audiobook;
use App\Services\UtilService;

class AudiobookRepository extends AbstractRepository
{
    private $utilService;

    public function __construct(UtilService $utilService)
    {
        parent::__construct();

        $this->utilService = $utilService;
    }

    public function getOneByPath(string $path): ?Audiobook
    {
        return $this->getOneById($this->utilService->getPathHash($path));
    }
}
