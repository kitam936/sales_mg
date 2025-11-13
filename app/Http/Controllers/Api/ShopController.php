<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->company_id;

        $shops = DB::table('shops')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->select('id as shop_id', 'shop_name')
            ->orderBy('shop_name')
            ->get();

        return response()->json(['shops' => $shops]);
    }
}
