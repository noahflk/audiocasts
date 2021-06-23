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

    public function sizeInMB(): int
    {
        return round($this->filesize / 1000 / 1000);
    }

    public function formattedDuration(): string
    {
        $t = round($this->playtime);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
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
