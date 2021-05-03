<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class Pathlib extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pathlib';
    }
}
