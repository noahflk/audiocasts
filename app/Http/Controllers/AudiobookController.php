<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use Illuminate\Http\Request;

class AudiobookController extends Controller
{
    public function show(Audiobook $audiobook)
    {
        return view('audiobooks.show', compact('audiobook'));
    }
}
