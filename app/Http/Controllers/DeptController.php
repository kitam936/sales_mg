<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use App\Http\Requests\StoreDeptRequest;
use App\Http\Requests\UpdateDeptRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Throwable;


class DeptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $depts= DB::table('depts')
        ->select('id', 'dept_name')
        ->orderBy('id', 'asc')
        ->get();


        // dd($depts);

        return Inertia::render('Depts/Index', [
            'depts' => $depts,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Depts/Create');
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeptRequest $request)
    {
        $request->validate([
            'dept_name' => ['required', 'string', 'max:255'],
            'dept_info' => ['string', 'max:255'],
        ]);

        // dd($request->all());

        try{
            DB::transaction(function()use($request){
                Dept::create([
                    'dept_name' => $request->dept_name,
                    'dept_info' => $request->dept_info,
                ]);

            },2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('depts.index')->with(['message'=>'登録されました','status'=>'info']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dept $dept)
    {
        $deptDetail = DB::table('depts')
            ->select('id', 'dept_name', 'dept_info')
            ->where('id', $dept->id)
            ->first();


        return Inertia::render('Depts/Show', [
            'dept' => $deptDetail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dept $dept)
    {
        $dept = Dept::FindOrFail($dept->id);
//
        // dd($dept);

        return Inertia::render('Depts/Edit', [
            'dept' => $dept,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeptRequest $request, Dept $dept)
    {
        $request->validate([
            'dept_name' => ['required', 'string', 'max:255'],
            'dept_info' => ['string', 'max:255'],
        ]);

        $dept = Dept::findOrFail($dept->id);

        try{
            DB::transaction(function()use($request, $dept){
                $dept->update([
                    'dept_name' => $request->dept_name,
                    'dept_info' => $request->dept_info,

                ]);
            },2);
        }
        catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('depts.index')->with(['message'=>'更新されました','status'=>'info']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dept $dept)
    {
        $dept->delete();

            return to_route('depts.index')->with(['message'=>'削除されました','status'=>'alert']);
    }
}
