<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles= DB::table('roles')
        ->select('id', 'role_name')
        ->orderBy('id', 'asc')
        ->get();


        // dd($roles);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Roles/Create');
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
            'role_info' => ['string', 'max:255'],
        ]);

        // dd($request->all());

        try{
            DB::transaction(function()use($request){
                Role::create([
                    'role_name' => $request->role_name,
                    'role_info' => $request->role_info,
                ]);

            },2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('roles.index')->with(['message'=>'登録されました','status'=>'info']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $RoleDetail = DB::table('roles')
            ->select('id', 'role_name', 'role_info')
            ->where('id', $role->id)
            ->first();


        return Inertia::render('Roles/Show', [
            'role' => $RoleDetail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role = Role::FindOrFail($role->id);
//
        // dd($role);

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
            'role_info' => ['string', 'max:255'],
        ]);

        $role = Role::findOrFail($role->id);

        try{
            DB::transaction(function()use($request, $role){
                $role->update([
                    'role_name' => $request->role_name,
                    'role_info' => $request->role_info,

                ]);
            },2);
        }
        catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('roles.index')->with(['message'=>'更新されました','status'=>'info']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

            return to_route('roles.index')->with(['message'=>'削除されました','status'=>'alert']);
    }
}
