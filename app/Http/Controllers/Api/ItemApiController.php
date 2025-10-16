<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemApiController extends Controller
{
    public function index()
    {
        return response()->json(Item::all());
    }
}
