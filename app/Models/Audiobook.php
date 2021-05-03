<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audiobook extends Model
{
    use HasFactory;

    public function coverPath(): string
    {
        return "storage/covers/" . $this->cover;
    }

    public function files()
    {
        return $this->hasMany("App\Models\File");
    }

    // Also removes files that belong to audiobook because of constraint in files_table migration
    public static function deleteWhereIDsNotIn($ids, $key = 'id')
    {
        $maxChunkSize = config('database.default') === 'sqlite' ? 999 : 65535;

        // If the number of entries is lower than, or equals to maxChunkSize, just go ahead.
        if (count($ids) <= $maxChunkSize) {
            static::whereNotIn($key, $ids)->delete();
            return;
        }

        // Otherwise, we get the actual IDs that should be deleted…
        $allIDs = static::select($key)->get()->pluck($key)->all();
        $whereInIDs = array_diff($allIDs, $ids);

        // …and see if we can delete them instead.
        if (count($whereInIDs) < $maxChunkSize) {
            static::whereIn($key, $whereInIDs)->delete();

            return;
        }

        // If that's not possible (i.e. this array has more than maxChunkSize elements, too)
        // then we'll delete chunk by chunk.
        static::deleteByChunk($ids, $key, $maxChunkSize);
    }

    public static function getAllCovers(): array
    {
        return static::whereNotNull('cover')->pluck('cover')->toArray();
    }
}
