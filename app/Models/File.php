<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $guarded = [];
    protected $keyType = 'string';

    public static function getAllCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->toArray();
    }

    public static function getUniqueCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->unique()->toArray();
    }
}
