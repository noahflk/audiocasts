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

    public function audiobook()
    {
        return $this->belongsTo("App\Models\Audiobook");
    }

    public function coverPath(): string
    {
        if ($this->cover) {
            return config('audiocasts.cover_directory_private') . $this->cover;
        }

        if ($this->audiobook->cover) {
            return config('audiocasts.cover_directory_private') . $this->audiobook->cover;

        }

        return 'images/cover-empty.jpg';
    }

    public static function getAllCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->toArray();
    }

    public static function getUniqueCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->unique()->toArray();
    }
}
