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
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::searchUsers($request->search)
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->join('depts', 'users.dept_id', '=', 'depts.id')
        ->Where('users.dept_id', 'like', '%' . $request->dept_id . '%')
        ->select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'users.user_info',
            'users.mailService',
            'users.role_id',
            'roles.role_name',
            'users.dept_id',
            'depts.dept_name'
        )
        ->orderBy('users.id', 'asc')
        ->paginate(50)
        ->withQueryString();

        $depts= DB::table('depts')
        ->select('id', 'dept_name')
        ->orderBy('id', 'asc')
        ->get();


        // dd($users, $depts);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'depts' => $depts,

        ]);
    }

    public function show(User $user)
    {
        // ユーザーの詳細を取得
        $login_user = Auth::user(); // ← これの方が安全

        // dd($user->id);
        $userDetail = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->join('depts', 'users.dept_id', '=', 'depts.id')
        ->where('users.id', $user->id)
        ->select(
            'users.id as id',
            'users.name',
            'users.email',
            'users.user_info',
            'users.mailService',
            'users.role_id',
            'roles.role_name',
            'users.dept_id',
            'depts.dept_name'
        )
        ->first();

        return Inertia::render('Users/Show', [
            'user' => $userDetail,
            'login_user' => $login_user,
        ]);
    }

    public function create()
    {
        $roles = DB::table('roles')
            ->select('id', 'role_name')
            ->orderBy('id', 'asc')
            ->get();

        $depts = DB::table('depts')
            ->select('id', 'dept_name')
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'depts' => $depts,
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
                'dept_id' => ['required', 'exists:depts,id'],

                // 'mailService' => ['required', 'boolean'],
            ]);

            // dd($request->all());

            try{
                DB::transaction(function()use($request){
                    User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'user_info' => $request->user_info,
                        'dept_id' => $request->dept_id,
                        'password' => Hash::make($request->password),
                        'role_id' => $request->role_id,
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

            $depts = DB::table('depts')
                ->select('id', 'dept_name')
                ->orderBy('id', 'asc')
                ->get();

            return Inertia::render('Users/Edit', [
                'user' => $user,
                'roles' => $roles,
                'depts' => $depts,
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
                'dept_id' => ['required', 'exists:depts,id'],
                'mailService' => [ 'boolean'],
            ]);

            $user = User::findOrFail($user->id);

            try{
                DB::transaction(function()use($request, $user){
                    $user->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'user_info' => $request->user_info,
                        'dept_id' => $request->dept_id,
                        'password' => Hash::make($request->password),
                        'role_id' => $request->role_id,
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
                'dept_id' => ['required', 'exists:depts,id'],
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
