<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoverImage
{
    public static function save($b64Image)
    {
        $imageName = Str::uuid() . '.' . 'jpg';
        Storage::disk('public')->put(config('audiocasts.cover_directory') . $imageName, $b64Image);
        return $imageName;
    }
}
