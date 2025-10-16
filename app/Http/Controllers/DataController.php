<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use \SplFileObject;
use Throwable;
use Inertia\Inertia;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\Sale;
use App\Models\Company;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Col;
use App\Models\Hinban;
use App\Models\Size;
use App\Models\Sku;
use App\Models\Unit;
use App\Models\Ym;
use App\Models\Yw;
use App\Models\Ymd;
use App\Models\Yy;

class DataController extends Controller
{

    public function menu()
    {
        return Inertia::render('Data/DataMenu');
    }
    public function index()
    {
        return Inertia::render('Data/DataIndex');

    }


    public function create()
    {
        return Inertia::render('Data/DataCreate');
    }



    public function unit_index(Request $request)
    {
        return Inertia::render('Data/UnitData', [
            'units' => Unit::all()
        ]);
    }

    public function brand_index(Request $request)
    {
        return Inertia::render('Data/BrandData', [
            'brands' => Brand::all()
        ]);
    }

    public function sku_index(Request $request)
    {
        return Inertia::render('Data/SkuData', [
            'skus' => Sku::all()
        ]);
    }

    public function col_index(Request $request)
    {
        return Inertia::render('Data/ColData', [
            'cols' => Col::all()
        ]);
    }

    public function size_index(Request $request)
    {
        return Inertia::render('Data/SizeData', [
            'sizes' => Size::all()
        ]);
    }

    public function Ym_index(Request $request)
    {
        return Inertia::render('Data/YmData', [
            'yms' => Ym::all()
        ]);
    }

    // public function Yw_index(Request $request)
    // {
    //     return Inertia::render('Data/YwData', [
    //         'yws' => Yw::all()
    //     ]);
    // }

    public function Ymd_index(Request $request)
    {
        return Inertia::render('Data/YmdData', [
            'ymds' => Ymd::all()
        ]);
    }

    // public function y_index(Request $request)
    // {
    //     return Inertia::render('Data/YData', [
    //         'ys' => Yy::all()
    //     ]);
    // }

    public function hinban_index(Request $request)
    {
        $products = Hinban::with('unit')
        ->where('year_code', 'LIKE', '%' . $request->year_code . '%')
        ->where('brand_id', 'LIKE', '%' . $request->brand_code . '%')
        ->where('unit_id', 'LIKE', '%' . $request->unit_code . '%')
        ->paginate(100);

        $years = Hinban::select('year_code')->groupBy('year_code')->orderByDesc('year_code')->get();
        $units = Unit::select('id')->orderBy('id')->get();
        $brands = Brand::select('id')->orderBy('id')->get();

        return Inertia::render('Data/HinbanData',[
            'products' => $products,
            'years'=> $years,
            'units' => $units,
            'brands' => $brands,]);
    }

    public function area_index(Request $request)
    {
        return Inertia::render('Data/AreaData', [
            'areas' => Area::all()
        ]);
    }

    public function company_index(Request $request)
    {
        return Inertia::render('Data/CompanyData', [
            'companies' => Company::all()
        ]);
    }

    public function shop_index(Request $request)
    {
        $shops = Shop::with('area', 'company')
            ->when($request->co_id, fn($q) => $q->where('company_id', 'LIKE', '%' . $request->co_id . '%'))
            ->paginate(15);

        $companies = Company::when($request->co_id, fn($q) => $q->where('id', 'LIKE', '%' . $request->co_id . '%'))
            ->select('id', 'co_name')
            ->get();

        return Inertia::render('Data/ShopData', [
            'shops' => $shops,
            'companies' => $companies,]);
    }

    public function sales_index()
    {
        return Inertia::render('Data/SalesData', [
            'sales' => Sale::with('shop.company')->orderByDesc('sales_date')->paginate(15)
        ]);
    }



    public function stock_index()
    {
        return Inertia::render('Data/StockData', [
            'stocks' => Stock::with('shop.company')->paginate(15)
        ]);
    }

    public function delete_index()
    {
        $YMs=DB::table('sales')
        ->select(['YM'])
        ->groupBy(['YM'])
        ->orderBy('YM','desc')
        ->get();
        $max_YM=Sale::max('YM');
        $max_YW=Sale::max('YW');

        $years=DB::table('hinbans')
        ->select(['year_code'])
        ->groupBy(['year_code'])
        ->orderBy('year_code','asc')
        ->get();

        $min_year=Hinban::min('year_code');

        return Inertia::render('Data/DeleteIndex', [
            'YMs' => $YMs,
            'max_YM' => $max_YM,
            'max_YW' => $max_YW,
            'years' => $years,
            'min_year' => $min_year,
        ]);

    }

    public function shop_edit($id)
    {
        $shop=Shop::findOrFail($id);

        return Inertia::render('Data/ShopEdit',[
            'shop' => $shop,]);

    }


    public function shop_destroy($id)
    {
        // dd('delete');
        Shop::findOrFail($id)->delete();

        return to_route('admin.data.shop_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function shop_destroy_ALL(Request $request)
    {
        $Stocks=Shop::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }


    public function sales_destroy(Request $request)
    {
        // dd($request->YM1,$request->YM2);
        DB::table('sales')
        ->where('sales.YM','>=',($request->YM1))
        ->where('sales.YM','<=',($request->YM2))
        ->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }



    public function stock_destroy(Request $request)
    {
        $Stocks=Stock::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function sku_destroy(Request $request)
    {
        $Stocks=Sku::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function hinban_destroy(Request $request)
    {
        // $Stocks=Hinban::query()->delete();
        DB::table('hinbans')
        ->where('hinbans.year_code','>=',($request->year1))
        ->where('hinbans.year_code','<=',($request->year2))
        ->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function company_destroy(Request $request)
    {
        $Stocks=Company::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function area_destroy(Request $request)
    {
        $Stocks=Area::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function unit_destroy(Request $request)
    {
        $Stocks=Unit::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function brand_destroy(Request $request)
    {
        $Stocks=Brand::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function col_destroy(Request $request)
    {
        $Stocks=Col::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }

    public function size_destroy(Request $request)
    {
        $Stocks=Size::query()->delete();

        return to_route('admin.data.delete_index')->with(['message'=>'削除されました','status'=>'alert']);
    }



    public function stock_upload(Request $request)
    {
        // タイムアウト対応？
        ini_set('max_execution_time',180);

        set_time_limit(150);
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('stock_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('stock_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				// $data_arr[$i]['id'] = $line[0];
				$data_arr[$i]['shop_id'] = $line[1];
				$data_arr[$i]['sku_id'] = $line[2];
				$data_arr[$i]['pcs'] = $line[3];
                $data_arr[$i]['zaikogaku'] = $line[4];
				$data_arr[$i]['created_at'] = $line[5];
				$data_arr[$i]['updated_at'] = $line[6];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('stocks')->insert($chunk);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function sales_upload0(Request $request)
    {
        // タイムアウト対応？
        ini_set('max_execution_time',180);

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        // dd($request);
		$file = $request->file('sales_data');
        // dd($file);

        file_put_contents($file, mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win'));


        DB::beginTransaction();

        try{
			//ファイルの読み込み
			$csv_arr = new \SplFileObject($file);
			$csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

			//csvの値格納用配列
			$data_arr = [];

            $count = 0; // 登録件数確認用

			foreach($csv_arr as $i=>$line){
				if ($line === [null]) continue;
				if($i == 0) continue;

				//配列に格納
				// $data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['sales_date'] = $line[1];
				$data_arr[$i]['shop_id'] = $line[2];
				$data_arr[$i]['sku_id'] = $line[3];
				$data_arr[$i]['pcs'] = $line[4];
                $data_arr[$i]['tanka'] = $line[5];
                $data_arr[$i]['kingaku'] = $line[6];
                $data_arr[$i]['Ym'] = $line[7];
                $data_arr[$i]['Yw'] = $line[8];
                $data_arr[$i]['Ymd'] = $line[9];
                $data_arr[$i]['Y'] = $line[10];
				$data_arr[$i]['created_at'] = $line[11];
				$data_arr[$i]['updated_at'] = $line[12];
                $count++;
			}

                //保存

			foreach(array_chunk($data_arr, 500) as $chunk){
				DB::transaction(function() use ($chunk){
					DB::table('sales')->insert($chunk);

				});

			}

			DB::commit();

            return view('data.result',compact('count'));

		}catch(Throwable $e){
			DB::rollback();
            Log::error($e);
            // throw $e;
            return to_route('admin.data.create')->with(['message'=>'エラーにより処理を中断しました。csvデータを確認してください。','status'=>'alert']);
		}
    }

    public function sales_upload(Request $request)
    {
        // タイムアウト対応？
        ini_set('max_execution_time',180);

        set_time_limit(150);
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('sales_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('sales_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

               //配列に格納
				// $data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['sales_date'] = $line[1];
				$data_arr[$i]['shop_id'] = $line[2];
				$data_arr[$i]['sku_id'] = $line[3];
				$data_arr[$i]['pcs'] = $line[4];
                $data_arr[$i]['tanka'] = $line[5];
                $data_arr[$i]['kingaku'] = $line[6];
                $data_arr[$i]['arari'] = $line[7];
                $data_arr[$i]['YM'] = $line[8];
                $data_arr[$i]['YW'] = $line[9];
                $data_arr[$i]['YMD'] = $line[10];
                $data_arr[$i]['Y'] = $line[11];
				$data_arr[$i]['created_at'] = $line[12];
				$data_arr[$i]['updated_at'] = $line[13];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('sales')->insert($chunk);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function hinban_upsert(Request $request)
    {
        // タイムアウト対応？
        ini_set('max_execution_time',180);

        set_time_limit(150);
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('hinban_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('hinban_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['brand_id'] = $line[1];
				$data_arr[$i]['unit_id'] = $line[2];
				$data_arr[$i]['year_code'] = $line[3];
				$data_arr[$i]['shohin_gun'] = $line[4];
                $data_arr[$i]['hinban_name'] = $line[5];
                $data_arr[$i]['m_price'] = $line[6];
                $data_arr[$i]['price'] = $line[7];
                $data_arr[$i]['cost'] = $line[8];
                $data_arr[$i]['hinban_info'] = $line[9];
                $data_arr[$i]['vendor_id'] = $line[10];
                $data_arr[$i]['designer_id'] = $line[11];
                $data_arr[$i]['face'] = $line[12];
				$data_arr[$i]['created_at'] = $line[13];
				$data_arr[$i]['updated_at'] = $line[14];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('hinbans')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function shop_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('shop_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('shop_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['company_id'] = $line[1];
				$data_arr[$i]['area_id'] = $line[2];
				$data_arr[$i]['shop_name'] = $line[3];
				$data_arr[$i]['shop_info'] = $line[4];
                $data_arr[$i]['is_selling'] = $line[5];
				$data_arr[$i]['created_at'] = $line[6];
				$data_arr[$i]['updated_at'] = $line[7];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('shops')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }




    public function company_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('co_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('co_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
				$data_arr[$i]['co_name'] = $line[1];
				$data_arr[$i]['co_info'] = $line[2];
                $data_arr[$i]['pic_id'] = $line[3];
				$data_arr[$i]['created_at'] = $line[4];
				$data_arr[$i]['updated_at'] = $line[5];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('companies')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }




    public function area_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('ar_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('ar_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
				$data_arr[$i]['area_name'] = $line[1];
                $data_arr[$i]['area_info'] = $line[2];
				$data_arr[$i]['created_at'] = $line[3];
				$data_arr[$i]['updated_at'] = $line[4];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('areas')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function unit_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('unit_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('unit_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['unit_code'] = $line[1];
                $data_arr[$i]['season_id'] = $line[2];
				$data_arr[$i]['season_name'] = $line[3];
				$data_arr[$i]['created_at'] = $line[4];
				$data_arr[$i]['updated_at'] = $line[5];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('units')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }


    public function brand_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('brand_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('brand_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['brand_name'] = $line[1];
				$data_arr[$i]['brand_info'] = $line[2];
				$data_arr[$i]['created_at'] = $line[3];
				$data_arr[$i]['updated_at'] = $line[4];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('brands')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }


    public function sku_upsert(Request $request)
    {
        // タイムアウト対応？
        ini_set('max_execution_time',180);

        set_time_limit(150);
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('sku_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('sku_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                //配列に格納
				$data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['hinban_id'] = $line[1];
				$data_arr[$i]['col_id'] = $line[2];
                $data_arr[$i]['size_id'] = $line[3];
                $data_arr[$i]['sku_image'] = $line[4];
				$data_arr[$i]['created_at'] = $line[5];
				$data_arr[$i]['updated_at'] = $line[6];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('skus')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function col_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('col_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('col_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                $data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['col_name'] = $line[1];
                $data_arr[$i]['created_at'] = $line[2];
                $data_arr[$i]['updated_at'] = $line[3];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('cols')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }



    public function size_upsert(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->file('size_data');

        if (!$file) {
            return to_route('admin.data.create')->with([
                'message' => 'ファイルが選択されていません',
                'status' => 'alert'
            ]);
        }

        // CSV を UTF-8 に変換して一時ファイルに保存
        $contents = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'SJIS-win');
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('size_csv_') . '.csv';
        file_put_contents($tmpPath, $contents);

        DB::beginTransaction();

        try {
            $csv_arr = new \SplFileObject($tmpPath);
            $csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

            $data_arr = [];
            $count = 0;

            foreach ($csv_arr as $i => $line) {
                if ($line === [null]) continue;
                if ($i == 0) continue;

                $data_arr[$i]['id'] = $line[0];
                $data_arr[$i]['size_name'] = $line[1];
                $data_arr[$i]['created_at'] = $line[2];
                $data_arr[$i]['updated_at'] = $line[3];
                $count++;
            }

            foreach (array_chunk($data_arr, 500) as $chunk) {
                DB::table('sizes')->upsert($chunk, ['id']);
            }

            DB::commit();

            // 一時ファイル削除
            unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => "登録されました（{$count}件）",
                'status' => 'info'
            ]);

        } catch (Throwable $e) {
            DB::rollback();
            Log::error($e);
            if (file_exists($tmpPath)) unlink($tmpPath);

            return to_route('admin.data.create')->with([
                'message' => 'エラーにより処理を中断しました。CSVデータを確認してください。',
                'status' => 'alert'
            ]);
        }
    }

    public function Ym_upsert(Request $request)
    {
        Ym::query()->delete();

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        // dd($request);
		$file = $request->file('ym_data');
        // dd($file);

        file_put_contents($file, mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win'));


        DB::beginTransaction();

        try{
			//ファイルの読み込み
			$csv_arr = new \SplFileObject($file);
			$csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

			//csvの値格納用配列
			$data_arr = [];

            $count = 0; // 登録件数確認用

			foreach($csv_arr as $i=>$line){
				if ($line === [null]) continue;
				if($i == 0) continue;


				//配列に格納
				$data_arr[$i]['YM'] = $line[0];
                $data_arr[$i]['prev_YM'] = $line[1];
                $count++;
			}

                //保存

			foreach(array_chunk($data_arr, 500) as $chunk){
				DB::transaction(function() use ($chunk){
					DB::table('yms')->upsert($chunk,['YM']);

				});

			}

			DB::commit();

            return view('data.result',compact('count'));

		}catch(Throwable $e){
			DB::rollback();
            Log::error($e);
            // throw $e;
            return to_route('admin.data.create')->with(['message'=>'エラーにより処理を中断しました。csvデータを確認してください。','status'=>'alert']);
		}
    }

    public function Yw_upsert(Request $request)
    {
        Yw::query()->delete();

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        // dd($request);
		$file = $request->file('yw_data');
        // dd($file);

        file_put_contents($file, mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win'));


        DB::beginTransaction();

        try{
			//ファイルの読み込み
			$csv_arr = new \SplFileObject($file);
			$csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

			//csvの値格納用配列
			$data_arr = [];

            $count = 0; // 登録件数確認用

			foreach($csv_arr as $i=>$line){
				if ($line === [null]) continue;
				if($i == 0) continue;


				//配列に格納
				$data_arr[$i]['YW'] = $line[0];
                $data_arr[$i]['prev_YW'] = $line[1];
                $count++;
			}

                //保存

			foreach(array_chunk($data_arr, 500) as $chunk){
				DB::transaction(function() use ($chunk){
					DB::table('yws')->upsert($chunk,['YW']);

				});

			}

			DB::commit();

            return view('data.result',compact('count'));

		}catch(Throwable $e){
			DB::rollback();
            Log::error($e);
            // throw $e;
            return to_route('admin.data.create')->with(['message'=>'エラーにより処理を中断しました。csvデータを確認してください。','status'=>'alert']);
		}
    }

    public function Ymd_upsert(Request $request)
    {

        Ymd::query()->delete();

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        // dd($request);
		$file = $request->file('ymd_data');
        // dd($file);

        file_put_contents($file, mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win'));


        DB::beginTransaction();

        try{
			//ファイルの読み込み
			$csv_arr = new \SplFileObject($file);
			$csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

			//csvの値格納用配列
			$data_arr = [];

            $count = 0; // 登録件数確認用

			foreach($csv_arr as $i=>$line){
				if ($line === [null]) continue;
				if($i == 0) continue;


				//配列に格納
				$data_arr[$i]['YMD'] = $line[0];
                $data_arr[$i]['date'] = $line[1];
                $data_arr[$i]['prev_YMD'] = $line[2];
                $count++;
			}

                //保存

			foreach(array_chunk($data_arr, 500) as $chunk){
				DB::transaction(function() use ($chunk){
					DB::table('ymds')->upsert($chunk,['YMD']);

				});

			}

			DB::commit();

            // return view('data.result',compact('count'));
            return to_route('admin.data.create')->with(['message'=>'登録されました','status'=>'info']);

		}catch(Throwable $e){
			DB::rollback();
            Log::error($e);
            // throw $e;
            return to_route('admin.data.create')->with(['message'=>'エラーにより処理を中断しました。csvデータを確認してください。','status'=>'alert']);
		}
    }

    public function Y_upsert(Request $request)
    {
        Yw::query()->delete();

        setlocale(LC_ALL, 'ja_JP.UTF-8');
        // dd($request);
		$file = $request->file('y_data');
        // dd($file);

        file_put_contents($file, mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win'));


        DB::beginTransaction();

        try{
			//ファイルの読み込み
			$csv_arr = new \SplFileObject($file);
			$csv_arr->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

			//csvの値格納用配列
			$data_arr = [];

            $count = 0; // 登録件数確認用

			foreach($csv_arr as $i=>$line){
				if ($line === [null]) continue;
				if($i == 0) continue;


				//配列に格納
				$data_arr[$i]['Y'] = $line[0];
                $data_arr[$i]['prev_Y'] = $line[1];
                $count++;
			}

                //保存

			foreach(array_chunk($data_arr, 500) as $chunk){
				DB::transaction(function() use ($chunk){
					DB::table('yys')->upsert($chunk,['Y']);

				});

			}

			DB::commit();

            return view('data.result',compact('count'));

		}catch(Throwable $e){
			DB::rollback();
            Log::error($e);
            // throw $e;
            return to_route('admin.data.create')->with(['message'=>'エラーにより処理を中断しました。csvデータを確認してください。','status'=>'alert']);
		}
    }


}
