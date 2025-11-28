<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use App\Models\User;
use App\Models\Image;
use App\Models\SkuImage;
use App\Models\Hinban;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable; // 追加
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;

class ImageController extends Controller
{

    public function image_create()
    {

        return Inertia::render('Hinbans/Image_Create');
    }


    public function store(Request $request)
    {
        try {
            $imageFiles = $request->file('files');

            if (!$imageFiles) {
                return back()->with(['message' => 'ファイルが選択されていません', 'status' => 'warning']);
            }

            // ✅ GDドライバーを指定してImageManager生成
            $manager = new ImageManager(new GdDriver());

            foreach ($imageFiles as $imageFile) {
                $file = is_array($imageFile) ? ($imageFile['image'] ?? null) : $imageFile;
                if (!$file || !$file->isValid()) continue;

                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $fileNameToStore = $originalName;
                $filePath = 'images/' . $fileNameToStore;

                // === 画像読み込み ===
                $img = $manager->read($file);

                // === 向き補正（Exifが有効なら実行）===
                try {
                    $img->orientate();
                } catch (Throwable $e) {
                    // orientate未対応環境ではスキップ
                }

                // === サイズ調整 ===
                $img->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // ✅ v3対応: エンコードには Encoder クラスを使用
                $encoded = $img->encode(new JpegEncoder(quality: 90));

                // === 保存処理 ===
                $isExist = Image::where('filename', $fileNameToStore)->exists();
                if ($isExist) {
                    Storage::delete($filePath);
                }
                Storage::put($filePath, (string)$encoded);

                // === DB登録（新規のみ）===
                if (!$isExist) {
                    Image::create([
                        'hinban_id' => $basename,
                        'filename' => $fileNameToStore,
                    ]);
                }
            }

            return to_route('admin.image_create')->with([
                'message' => '品番画像情報を登録しました',
                'status' => 'info'
            ]);
        } catch (Throwable $e) {
            report($e);
            return back()->with([
                'message' => '画像登録中にエラーが発生しました: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }

    public function sku_store(Request $request)
    {

        try {
            $imageFiles = $request->file('files');

            if (!$imageFiles) {
                return back()->with(['message' => 'ファイルが選択されていません', 'status' => 'warning']);
            }

            // ✅ GDドライバー指定
            $manager = new ImageManager(new GdDriver());

            foreach ($imageFiles as $imageFile) {
                // 複数ファイル配列（files[][image]）対応
                $file = is_array($imageFile) ? ($imageFile['image'] ?? null) : $imageFile;
                if (!$file || !$file->isValid()) continue;

                $originalName = $file->getClientOriginalName();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $fileNameToStore = $originalName;
                $filePath = 'sku_images/' . $fileNameToStore;

                // === 画像読み込み ===
                $img = $manager->read($file);

                // === 向き補正（EXIF有効時）===
                try {
                    $img->orientate();
                } catch (Throwable $e) {
                    // orientate未対応の場合はスキップ
                }

                // === サイズ調整（アスペクト比維持・最大800px）===
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // ✅ v3.1対応：エンコーダークラスを使用
                $encoded = $img->encode(new JpegEncoder(quality: 90));

                // === 同名ファイル存在チェック ===
                $isExist = SkuImage::where('filename', $fileNameToStore)->exists();

                if ($isExist) {
                    Storage::delete($filePath);
                }

                // === ストレージ保存 ===
                Storage::put($filePath, (string)$encoded);

                // === DB登録（新規時のみ）===
                if (!$isExist) {
                    SkuImage::create([
                        'sku_id' => $basename,   // 例：ファイル名がSKU IDを表す
                        'filename' => $fileNameToStore,
                    ]);
                }
            }

            return to_route('admin.image_create')->with([
                'message' => 'SKU画像情報を登録しました',
                'status' => 'info'
            ]);
        } catch (Throwable $e) {
            report($e);
            return back()->with([
                'message' => 'SKU画像登録中にエラーが発生しました: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }



    public function show(string $id)
    {
        //
    }

    public function image_show($id)
    {
        $login_user =  DB::table('users')
        ->where('users.id',Auth::id())
        ->first();

        $image = DB::table('hinbans')
        ->leftjoin('images','hinbans.id','images.hinban_id')
        ->where('images.hinban_id',($id))
        ->select('images.hinban_id','hinbans.hinban_name','images.filename','hinbans.m_price',
        'hinbans.hinban_info','hinbans.cost')
        ->first();
        $sku_images = DB::table('skus')
        ->leftjoin('sku_images','skus.id','sku_images.sku_id')
        ->join('sizes','skus.size_id','sizes.id')
        ->join('hinbans','hinbans.id','skus.hinban_id')
        ->where('skus.hinban_id',($id))
        ->where('skus.col_id','<>',99)
        ->select('sku_images.sku_id','skus.col_id','sku_images.filename','sizes.size_name')
        ->get();

        // dd($sku_images,$image,$login_user);

        return Inertia::render('Hinbans/Image_Show',compact('image','sku_images','login_user'));


    }



    public function sku_image_show($id)
    {
        $login_user =  DB::table('users')
        ->where('users.id',Auth::id())
        ->first();
        $image = DB::table('sku_images')
        ->join('skus','skus.id','sku_images.sku_id')
        ->join('hinbans','hinbans.id','skus.hinban_id')
        ->where('sku_images.sku_id',($id))
        ->select('sku_images.sku_id','skus.hinban_id','skus.col_id','skus.size_id','hinbans.hinban_name','sku_images.filename')
        ->first();
        $sku_images = DB::table('sku_images')
        ->join('skus','skus.id','sku_images.sku_id')
        ->join('hinbans','hinbans.id','skus.hinban_id')
        ->where('sku_images.sku_id',($id))
        ->select('sku_images.sku_id','skus.col_id','sku_images.filename')
        ->get();

        // dd($sku_images);

        return Inertia::render('Hinbans/Sku_Image_Show',compact('image','sku_images','login_user'));

        // dd($login_user,$image,$sku_images);
        // return view('product.sku_image_show',compact('image','sku_images','login_user'));
    }

    /**
     * Show the form for editing the specified resource.
     */


    public function image_destroy($id)
    {
        $image = DB::table('images')
        ->where('images.hinban_id',($id))
        ->select('images.hinban_id','images.filename')
        ->first();
        $hinban = $image->hinban_id;
        $filePath = 'images/' . $image->filename;
        // dd($image,$hinban,$filePath);
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
        }

        Image::Where('hinban_id','=',$id)->delete();

        return to_route('hinbans.index')->with(['message'=>'画像を削除しました',
        'status'=>'alert']);
    }

    public function sku_image_destroy($id)
    {
        $sku_image = DB::table('sku_images')
        ->where('sku_images.sku_id',($id))
        ->select('sku_images.sku_id','sku_images.filename')
        ->first();
        $sku = $sku_image->sku_id;
        $filePath = 'sku_images/' . $sku_image->filename;
        // dd($image,$hinban,$filePath);
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
        }

        SkuImage::Where('sku_id','=',$id)->delete();

        return to_route('image_show')->with(['message'=>'SKU画像を削除しました',
        'status'=>'alert']);
    }

    public function hinban_image_check(Request $request)
    {
        // 年度リスト
        $years = DB::table('hinbans')
        ->select('year_code')
        ->where('year_code','<=',50)
        ->groupBy('year_code')
        ->orderByDesc('year_code')
        ->get();

        // Face リスト
        $faces = DB::table('hinbans')
            ->whereNotNull('face')
            ->select('face')
            ->groupBy('face')
            ->orderBy('face','asc')
            ->get();

        // Seasons リスト
        $seasons = DB::table('units')
            ->select(['season_id','season_name'])
            ->groupBy(['season_id','season_name'])
            ->orderBy('season_id','asc')
            ->get();

        // Units リスト（season_code があれば絞る）
        $units = DB::table('units')
            ->where('id','<=',12)
            // ->when($request->season_code, fn($q) => $q->where('season_id', (int)$request->season_code))
            ->select(['id','unit_code'])
            ->groupBy(['id','unit_code'])
            ->orderBy('id','asc')
            ->get();

        // Brands リスト
        $brands = DB::table('brands')
            ->select('id')
            ->groupBy('id')
            ->orderBy('id','asc')
            ->get();

        // 画像未登録の品番を取得（フィルター条件付き）
        $images = DB::table('hinbans')
            ->join('units','units.id','=','hinbans.unit_id')
            ->leftJoin('images', function($join){
                $join->on('hinbans.id','=','images.hinban_id');
            })
            ->where('hinbans.vendor_id','<>',8200)
            ->when($request->year_code, fn($q) => $q->where('hinbans.year_code', (int)$request->year_code))
            // ->when($request->season_code, fn($q) => $q->where('units.season_id', (int)$request->season_code))
            ->when($request->unit_code, fn($q) => $q->where('units.unit_code', (int)$request->unit_code))
            ->when($request->face, fn($q) => $q->where('hinbans.face','LIKE','%'.$request->face.'%'))
            ->when($request->brand_code, fn($q) => $q->where('hinbans.brand_id', (int)$request->brand_code))
            ->where('hinbans.year_code','>=',25)
            ->whereNull('images.hinban_id')
            ->select(
                'hinbans.id as hinban',
                'hinbans.unit_id',
                'hinbans.face',
                'hinbans.hinban_name',
                'hinbans.m_price',
                'hinbans.brand_id',
                'units.season_name',
                'hinbans.price'
            )

            ->orderBy('hinbans.year_code','asc')
            ->orderBy('hinbans.brand_id','asc')
            ->orderBy('hinbans.unit_id','asc')
            ->orderBy('hinbans.id','asc')
            ->paginate(100)
            ->appends($request->only(['year_code','unit_code','face','brand_code']));

        // Inertia レンダリング
        return Inertia::render('Hinbans/HinbanImageCheck', [
            'images' => $images,
            'seasons' => $seasons,
            'units' => $units,
            'years' => $years,
            'brands' => $brands,
            'faces' => $faces,
            'filters' => [
                'year_code' => $request->year_code,
                // 'season_code' => $request->season_code,
                'unit_code' => $request->unit_code,
                'face' => $request->face,
                'brand_code' => $request->brand_code,
            ],
        ]);
                // return view('product.hinban_image_check',compact('images','seasons','units','years','brands','faces'));
    }

    public function hinban_image_csv(Request $request)
    {
        $query = DB::table('hinbans')
            ->join('units','units.id','=','hinbans.unit_id')
            ->leftJoin('images', function($join){
                $join->on('hinbans.id','=','images.hinban_id');
            })
            ->where('hinbans.vendor_id','<>',8200)
            ->when($request->year_code, fn($q) => $q->where('hinbans.year_code', (int)$request->year_code))
            // ->when($request->season_code, fn($q) => $q->where('units.season_id', (int)$request->season_code))
            ->when($request->unit_code, fn($q) => $q->where('units.unit_code', (int)$request->unit_code))
            ->when($request->face, fn($q) => $q->where('hinbans.face','LIKE','%'.$request->face.'%'))
            ->when($request->brand_code, fn($q) => $q->where('hinbans.brand_id', (int)$request->brand_code))
            ->where('hinbans.year_code','>=',25)
            ->whereNull('images.hinban_id')
            ->select(
                'hinbans.id as hinban',
                'hinbans.unit_id',
                'hinbans.face',
                'hinbans.brand_id',

            );

        $data = $query->get();

        $filename = 'hinban_images_'.date('Ymd_His').'.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $columns = ['Brand','UNIT','Face','Hinban'];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output','w');
            fputcsv($file, $columns);
            foreach($data as $row){
                fputcsv($file, [
                    $row->brand_id,
                    $row->unit_id,
                    $row->face,
                    $row->hinban,

                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sku_image_check(Request $request)
    {
        // 年度リスト
        $years = DB::table('hinbans')
        ->select('year_code')
        ->where('year_code','<=',50)
        ->groupBy('year_code')
        ->orderByDesc('year_code')
        ->get();

        // Face リスト
        $faces = DB::table('hinbans')
            ->whereNotNull('face')
            ->select('face')
            ->groupBy('face')
            ->orderBy('face','asc')
            ->get();

        // Seasons リスト
        $seasons = DB::table('units')
            ->select(['season_id','season_name'])
            ->groupBy(['season_id','season_name'])
            ->orderBy('season_id','asc')
            ->get();

        // Units リスト（season_code があれば絞る）
        $units = DB::table('units')
            ->where('id','<=',12)
            // ->when($request->season_code, fn($q) => $q->where('season_id', (int)$request->season_code))
            ->select(['id','unit_code'])
            ->groupBy(['id','unit_code'])
            ->orderBy('id','asc')
            ->get();

        // Brands リスト
        $brands = DB::table('brands')
            ->select('id')
            ->groupBy('id')
            ->orderBy('id','asc')
            ->get();

        // 画像未登録の品番を取得（フィルター条件付き）
        $sku_images = DB::table('skus')
            ->leftjoin('sku_images','skus.id','sku_images.sku_id')
            ->join('sizes','skus.size_id','sizes.id')
            ->join('hinbans','hinbans.id','skus.hinban_id')
            ->join('units','units.id','=','hinbans.unit_id')
            ->where('hinbans.vendor_id','<>',8200)
            ->when($request->year_code, fn($q) => $q->where('hinbans.year_code', (int)$request->year_code))
            // ->when($request->season_code, fn($q) => $q->where('units.season_id', (int)$request->season_code))
            ->when($request->unit_code, fn($q) => $q->where('units.unit_code', (int)$request->unit_code))
            ->when($request->face, fn($q) => $q->where('hinbans.face','LIKE','%'.$request->face.'%'))
            ->when($request->brand_code, fn($q) => $q->where('hinbans.brand_id', (int)$request->brand_code))
            ->where('hinbans.year_code','>=',25)
            ->whereNull('sku_images.sku_id')
            ->where('skus.col_id','<>',99)
            ->where('hinbans.year_code','>=',25)
            ->select('hinbans.brand_id','units.season_name','hinbans.unit_id as unit','hinbans.face','skus.id as sku','hinbans.id as hinban','hinbans.hinban_name','skus.col_id','skus.size_id')
            ->paginate(100)
            ->appends($request->only(['year_code','unit_code','face','brand_code']));

        // dd($sku_images);
        // Inertia レンダリング
        return Inertia::render('Hinbans/SkuImageCheck', [
            'sku_images' => $sku_images,
            'seasons' => $seasons,
            'units' => $units,
            'years' => $years,
            'brands' => $brands,
            'faces' => $faces,
            'filters' => [
                'year_code' => $request->year_code,
                // 'season_code' => $request->season_code,
                'unit_code' => $request->unit_code,
                'face' => $request->face,
                'brand_code' => $request->brand_code,
            ],
        ]);
        // return view('product.sku_image_check',compact('sku_images','seasons','units','years','brands','faces'));
    }

    public function sku_image_csv(Request $request)
    {
        $query = DB::table('skus')
            ->join('hinbans','hinbans.id','skus.hinban_id')
            ->join('units','units.id','=','hinbans.unit_id')
            ->leftJoin('sku_images', function($join){
                $join->on('skus.id','=','sku_images.sku_id');
            })
            ->where('hinbans.vendor_id','<>',8200)
            ->when($request->year_code, fn($q) => $q->where('hinbans.year_code', (int)$request->year_code))
            // ->when($request->season_code, fn($q) => $q->where('units.season_id', (int)$request->season_code))
            ->when($request->unit_code, fn($q) => $q->where('units.unit_code', (int)$request->unit_code))
            ->when($request->face, fn($q) => $q->where('hinbans.face','LIKE','%'.$request->face.'%'))
            ->when($request->brand_code, fn($q) => $q->where('hinbans.brand_id', (int)$request->brand_code))
            ->where('hinbans.year_code','>=',25)
            ->where('skus.col_id','<>',99)
            ->whereNull('sku_images.sku_id')
            ->select(
                'hinbans.year_code',
                'hinbans.brand_id',
                'hinbans.unit_id',
                'hinbans.face',
                'hinbans.id as hinban',
                'skus.col_id',
                'skus.size_id'
            );

        $data = $query->get();

        $filename = 'sku_images_'.date('Ymd_His').'.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $columns = ['Year','Brand','UNIT','Face','Hinban','Color ID','Size ID'];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output','w');
            fputcsv($file, $columns);
            foreach($data as $row){
                fputcsv($file, [
                    $row->year_code,
                    $row->brand_id,
                    $row->unit_id,
                    $row->face,
                    $row->hinban,
                    $row->col_id,
                    $row->size_id,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
