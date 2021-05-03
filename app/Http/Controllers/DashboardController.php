<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $audiobooks = Audiobook::all();

        return view("audiobooks.index", compact("audiobooks"));
    }
}
