<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use Inertia\Inertia;
use Throwable; // 追加
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $login_user = Auth::user(); // ← これの方が安全
        $shops = Shop::searchshops($request->search)
        ->join('companies', 'shops.company_id', '=', 'companies.id')
        ->join('areas', 'shops.area_id', '=', 'areas.id')
        ->Where('shops.area_id', 'like', '%' . $request->area_id . '%')
        ->where('shops.company_id', 'like', '%' . $request->company_id . '%')
        ->where('shops.is_selling', 'like', '%' . $request->is_selling . '%')
        ->where('shops.id','>=',1100)
        ->where('shops.id','<',7000)
        ->select(
            'shops.id as shop_id',
            'shops.shop_name',
            'companies.id as company_id',
            'companies.co_name',
            'areas.id as area_id',
            'areas.area_name',
            'shops.shop_info',
            'shops.is_selling',
        )
        ->orderBy('shops.id', 'asc')
        ->paginate(50)
        ->withQueryString();

        $areas= DB::table('areas')
        ->select('id', 'area_name')
        ->orderBy('id', 'asc')
        ->get();

        $companies= DB::table('companies')
        ->where('id','>=',1000)
        ->where('id','<',7000)
        ->select('id', 'co_name')
        ->orderBy('id', 'asc')
        ->get();


        // dd($shops, $areas, $companies);

        return Inertia::render('Shops/Index', [
            'shops' => $shops,
            'areas' => $areas,
            'companies' => $companies,
            'login_user' => $login_user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = DB::table('areas')
            ->select('id', 'area_name')
            ->orderBy('id', 'asc')
            ->get();

        $companies = DB::table('companies')
            ->where('id','>=',1000)
            ->where('id','<',7000)
            ->select('id', 'co_name')
            ->orderBy('id', 'asc')
            ->get();

        // dd($areas, $companies);
        return Inertia::render('Shops/Create', [
            'areas' => $areas,
            'companies' => $companies,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShopRequest $request)
    {
        $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'shop_info' => ['string', 'max:255'],
            'area_id' => ['required', 'exists:areas,id'],
            // 'is_selling' => ['required', 'boolean'],
        ]);

        // dd($request->all());

        try{
            DB::transaction(function()use($request){
                Shop::create([
                    'id' => $request->shop_id,
                    'shop_name' => $request->shop_name,
                    'company_id' => $request->company_id,
                    'shop_info' => $request->shop_info,
                    'area_id' => $request->area_id,
                    'is_selling' => 1,
                ]);

            },2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('shops.index')->with(['message'=>'登録されました','status'=>'info']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        // ユーザーの詳細を取得
        $login_user = Auth::user(); // ← これの方が安全

        $shopDetail = DB::table('shops')
        ->join('areas', 'shops.area_id', '=', 'areas.id')
        ->join('companies', 'shops.company_id', '=', 'companies.id')
        ->where('shops.id', $shop->id)
        ->select(
            'shops.id as id',
            'shops.shop_name',
            'shops.company_id',
            'companies.co_name',
            'shops.shop_info',
            'shops.area_id',
            'areas.area_name',
            'shops.is_selling'
        )
        ->first();

        return Inertia::render('Shops/Show', [
            'shop' => $shopDetail,
            'login_user' => $login_user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        $areas = DB::table('areas')
        ->select('id', 'area_name')
        ->orderBy('id', 'asc')
        ->get();

        $companies = DB::table('companies')
            ->select('id', 'co_name')
            ->orderBy('id', 'asc')
            ->get();

        // dd($areas, $companies);

        return Inertia::render('Shops/Edit', [
            'shop' => $shop,
            'areas' => $areas,
            'companies' => $companies,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopRequest $request, Shop $shop)
    {
        $request->validate([
            // 'id' => ['required', 'exists:shops,id'],
            'shop_name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'shop_info' => ['string', 'max:255'],
            'area_id' => ['required', 'exists:areas,id'],
            'is_selling' => ['required', 'boolean'],
        ]);

        $shop = Shop::findOrFail($shop->id);

        try{
            DB::transaction(function()use($request, $shop){
                $shop->update([
                    'id' => $request->id,
                    'shop_name' => $request->shop_name,
                    'company_id' => $request->company_id,
                    'shop_info' => $request->shop_info,
                    'area_id' => $request->area_id,
                    'is_selling' => $request->is_selling,
                ]);
            },2);
        }
        catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('shops.index')->with(['message'=>'更新されました','status'=>'info']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();

        return to_route('shops.index')->with(['message'=>'削除されました','status'=>'alert']);
    }
}
