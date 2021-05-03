<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public static function getAllCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->toArray();
    }
}
