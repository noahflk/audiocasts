<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use Illuminate\Http\Request;

class AudiobookController extends Controller
{
    public function show(Audiobook $audiobook)
    {
        $count = $audiobook->files()->count();
        $size = round($audiobook->files()->sum('filesize') / 1000 / 1000);
        $duration = $this->getTime($audiobook->files()->sum('playtime'));

        return view('audiobooks.show', compact('audiobook', 'count', 'size', 'duration'));
    }

    private function getTime($seconds)
    {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    }
}
