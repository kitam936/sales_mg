<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function menu()
    {

        return Inertia::render('Menu');
    }
}
