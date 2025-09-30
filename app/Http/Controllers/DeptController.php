<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use App\Http\Requests\StoreDeptRequest;
use App\Http\Requests\UpdateDeptRequest;

class DeptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreDeptRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dept $dept)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dept $dept)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeptRequest $request, Dept $dept)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dept $dept)
    {
        //
    }
}
