<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App_models\Role;
use Inertia\Inertia;
use Throwable; // 追加



class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::searchUsers($request->search)
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->join('shops', 'users.shop_id', '=', 'shops.id')
        ->Where('users.shop_id', 'like', '%' . $request->shop_id . '%')
        ->select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'users.postcode',
            'users.address',
            'users.tel',
            'users.user_info',
            'users.mailService',
            'users.role_id',
            'roles.role_name',
            'users.shop_id',
            'shops.shop_name'
        )
        ->orderBy('users.id', 'asc')
        ->paginate(50)
        ->withQueryString();

        $shops= DB::table('shops')
        ->select('id', 'shop_name')
        ->orderBy('id', 'asc')
        ->get();


        // dd($users, $shops);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'shops' => $shops,

        ]);
    }

    public function show(User $user)
    {
        // ユーザーの詳細を取得

        // dd($user->id);
        $userDetail = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->join('shops', 'users.shop_id', '=', 'shops.id')
        ->where('users.id', $user->id)
        ->select(
            'users.id as id',
            'users.name',
            'users.email',
            'users.postcode',
            'users.address',
            'users.tel',
            'users.user_info',
            'users.mailService',
            'users.role_id',
            'roles.role_name',
            'users.shop_id',
            'shops.shop_name'
        )
        ->first();

        return Inertia::render('Users/Show', [
            'user' => $userDetail
        ]);
    }

    public function create()
    {
        $roles = DB::table('roles')
            ->select('id', 'role_name')
            ->orderBy('id', 'asc')
            ->get();

        $shops = DB::table('shops')
            ->select('id', 'shop_name')
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'shops' => $shops,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

        public function store(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'user_info' => ['string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role_id' => ['required', 'exists:roles,id'],
                'shop_id' => ['required', 'exists:shops,id'],
                'postcode'=> ['nullable', 'string', 'max:10'],
                'address'  => ['nullable', 'string', 'max:255'],
                'tel' => ['nullable', 'string', 'max:15'],
                // 'mailService' => ['required', 'boolean'],
            ]);

            // dd($request->all());

            try{
                DB::transaction(function()use($request){
                    User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'user_info' => $request->user_info,
                        'shop_id' => $request->shop_id,
                        'password' => Hash::make($request->password),
                        'role_id' => $request->role_id,
                        'postcode' => $request->postcode,
                        'address' => $request->address,
                        'tel' => $request->tel,
                        'mailService' => 1,
                    ]);

                },2);
            }catch(Throwable $e){
                Log::error($e);
                throw $e;
            }

            return to_route('users.index')->with(['message'=>'登録されました','status'=>'info']);
        }

        public function edit(User $user)
        {
            $roles = DB::table('roles')
                ->select('id', 'role_name')
                ->orderBy('id', 'asc')
                ->get();

            $shops = DB::table('shops')
                ->select('id', 'shop_name')
                ->orderBy('id', 'asc')
                ->get();

            return Inertia::render('Users/Edit', [
                'user' => $user,
                'roles' => $roles,
                'shops' => $shops,
                'old' => session()->getOldInput(),
                'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
            ]);
        }

        public function update0(Request $request, User $user)
        {
            // dd($request->all());
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'user_info' => ['string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role_id' => ['required', 'exists:roles,id'],
                'shop_id' => ['required', 'exists:shops,id'],
                'postcode'=> ['nullable', 'string', 'max:10'],
                'address'  => ['nullable', 'string', 'max:255'],
                'tel' => ['nullable', 'string', 'max:15'],
                'mailService' => [ 'boolean'],
            ]);

            $user = User::findOrFail($user->id);

            try{
                DB::transaction(function()use($request, $user){
                    $user->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'user_info' => $request->user_info,
                        'shop_id' => $request->shop_id,
                        'password' => Hash::make($request->password),
                        'role_id' => $request->role_id,
                        'postcode' => $request->postcode,
                        'address' => $request->address,
                        'tel' => $request->tel,
                        'mailService' => $request->mailService ,
                    ]);
                },2);
            }
            catch(Throwable $e){
                Log::error($e);
                throw $e;
            }

            return to_route('users.index')->with(['message'=>'更新されました','status'=>'info']);
        }

        public function update(Request $request, User $user)
        {
            // dd($request->all());
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'user_info' => ['nullable', 'string', 'max:255'],
                'role_id' => ['required', 'exists:roles,id'],
                'shop_id' => ['required', 'exists:shops,id'],
                'postcode'=> ['nullable', 'string', 'max:10'],
                'address'  => ['nullable', 'string', 'max:255'],
                'tel' => ['nullable', 'string', 'max:15'],
                'mailService' => ['required', 'boolean'],
            ];

            if ($request->filled('password')) {
                $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            }

            $validated = $request->validate($rules);

            $user->fill($validated);

            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return to_route('users.index')->with(['message'=>'更新されました','status'=>'info']);
        }


        public function destroy(User $user)
        {
            $user->delete();

            return to_route('users.index')->with(['message'=>'削除されました','status'=>'alert']);
        }

}
