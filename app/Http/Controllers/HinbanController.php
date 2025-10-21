<?php

namespace App\Http\Controllers;

use App\Models\Hinban;
use App\Http\Requests\StoreHinbanRequest;
use App\Http\Requests\UpdateHinbanRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable; // 追加

class HinbanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $products = Hinban::with('unit')->paginate(50);

        $images = DB::table('hinbans')
        ->join('units','units.id','=','hinbans.unit_id')
        ->leftjoin('images','hinbans.id','=','images.hinban_id')
        ->where('hinbans.vendor_id','<>',8200)
        ->where('hinbans.year_code','LIKE','%'.($request->year_code).'%')
        ->where('units.season_id','LIKE','%'.($request->season_code).'%')
        ->where('units.unit_code','LIKE','%'.($request->unit_code).'%')
        ->where('hinbans.face','LIKE','%'.($request->face).'%')
        ->where('hinbans.brand_id','LIKE','%'.($request->brand_code).'%')
        ->where('hinbans.id','LIKE','%'.($request->hinban_code).'%')
        ->select(['hinbans.year_code','hinbans.brand_id','hinbans.unit_id','units.season_id','units.season_name','hinbans.id as hinban_id','hinbans.hinban_name','hinbans.m_price','hinbans.price','hinbans.face','images.filename'])
        ->orderBy('hinbans.year_code','desc')
        ->orderBy('hinbans.brand_id','asc')
        ->orderBy('units.season_id','desc')
        ->orderBy('hinban_id','asc')
        ->paginate(20);
        // ->get();
        $years=DB::table('hinbans')
        ->select(['year_code'])
        ->where('year_code','<=',50)
        ->groupBy(['year_code'])
        ->orderBy('year_code','desc')
        ->get();
        $faces=DB::table('hinbans')
        ->whereNotNull('face')
        ->select(['face'])
        ->groupBy(['face'])
        ->orderBy('face','asc')
        ->get();
        $seasons=DB::table('units')
        ->select(['season_id','season_name'])
        ->groupBy(['season_id','season_name'])
        ->orderBy('season_id','asc')
        ->get();
        $units=DB::table('units')
        ->where('units.id','<=',12)
        ->where('units.season_id','LIKE','%'.$request->season_code.'%')
        ->select(['id','unit_code'])
        ->groupBy(['id','unit_code'])
        ->orderBy('id','asc')
        ->get();
        $brands=DB::table('brands')
        ->select(['id'])
        ->groupBy(['id'])
        ->orderBy('id','asc')
        ->get();

        // dd($images, $products, $seasons, $units, $years, $brands, $faces);

        return Inertia::render('Hinbans/Image_Index', [
            'images' => $images,
            'products' => $products,
            'seasons' => $seasons,
            'units' => $units,
            'years' => $years,
            'brands' => $brands,
            'faces' => $faces,
            'filters' => $request->only(['year_code','season_code','unit_code','brand_code','face','hinban_code']),
        ]);



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHinbanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hinban $hinban)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hinban $hinban)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHinbanRequest $request, Hinban $hinban)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hinban $hinban)
    {
        //
    }
}
