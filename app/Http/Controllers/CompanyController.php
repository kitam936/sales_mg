<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable; // 追加

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::searchCompanies($request->search)
            ->join('users', 'companies.pic_id', '=', 'users.id')
            ->Where('companies.pic_id', 'like', '%' . $request->pic_id . '%')
            ->select(
                'companies.id as company_id',
                'companies.co_name',
                'companies.co_info',
                'companies.pic_id',
                'users.name as pic_name'
            )
            ->orderBy('companies.id', 'asc')
            ->paginate(50)
            ->withQueryString();


        $pics= DB::table('users')
            ->where('dept_id', '=', 2)
            ->select('id', 'name')
            ->orderBy('id', 'asc')
            ->get();

        // dd($companies, $pics);

        return Inertia::render('Company/Index', [
            'companies' => $companies,
            'pics' => $pics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pics= DB::table('users')
            ->where('dept_id', '=', 2)
            ->orwhere('dept_id', '=', 9)
            ->select('id', 'name')
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Company/Create', [
            'pics' => $pics,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $request->validate([
            'co_id' => ['unique:companies,id'],
            'co_name' => ['required', 'string', 'max:255'],
            'co_info' => ['string', 'max:255'],
            'pic_id' => ['required', 'exists:users,id'],
        ]);

        // dd($request->all());

        try{
            DB::transaction(function()use($request){
                Company::create([
                    'id' => $request->co_id,
                    'co_name' => $request->co_name,
                    'co_info' => $request->co_info,
                    'pic_id' => $request->pic_id,
                ]);

            },2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('company.index')->with(['message'=>'登録されました','status'=>'info']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $companyDetail = DB::table('companies')
        ->join('users', 'companies.pic_id', '=', 'users.id')
        ->where('companies.id', $company->id)
        ->select(
            'companies.id as co_id',
            'companies.co_name',
            'companies.co_info',
            'companies.pic_id',
            'users.name as pic_name',
        )

        ->first();

        return Inertia::render('Company/Show', [
            'company' => $companyDetail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $pics= DB::table('users')
            ->where('dept_id', '=', 2)
            ->orwhere('dept_id', '=', 9)
            ->select('id', 'name')
            ->orderBy('id', 'asc')
            ->get();

            // dd($company, $pics);

        return Inertia::render('Company/Edit', [
            'company' => $company,
            'pics' => $pics,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $request->validate([
            // 'co_id' => ['unique:companies,id,'.$company->id],
            'co_name' => ['required', 'string', 'max:255'],
            'co_info' => ['string', 'max:255'],
            'pic_id' => ['required', 'exists:users,id'],
        ]);

        $companies = Company::findOrFail($company->id);

        try{
            DB::transaction(function()use($request, $company){
                $company->update([
                    'co_id' => $request->co_id,
                    'co_name' => $request->co_name,
                    'co_info' => $request->co_info,
                    'pic_id' => $request->pic_id,
                ]);
            },2);
        }
        catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return to_route('company.index')->with(['message'=>'更新されました','status'=>'info']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return to_route('company.index')->with(['message'=>'削除されました','status'=>'alert']);
    }
}
