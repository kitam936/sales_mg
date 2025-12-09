<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Http\UploadedFile; // ← これを先頭に追加
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Inertia\Inertia;
use Throwable; // 追加
use Intervention\Image\Imagick\ImagickDriver;
// use Intervention\Image\Gd\GdDriver;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 修正
        $userId = User::findOrFail(Auth::id())->id;

        $reports = DB::table('reports')
            ->join('shops', 'shops.id', '=', 'reports.shop_id')
            ->join('companies', 'companies.id', '=', 'shops.company_id')
            ->join('areas', 'areas.id', '=', 'shops.area_id')
            ->leftJoin('comments', 'comments.report_id', '=', 'reports.id')
            ->leftJoin('report_reads', function ($join) use ($userId) {
                $join->on('reports.id', '=', 'report_reads.report_id')
                    ->where('report_reads.user_id', '=', $userId);
            })
            ->leftJoin('comment_reads', function ($join) use ($userId) {
                $join->on('comments.id', '=', 'comment_reads.comment_id')
                    ->where('comment_reads.user_id', '=', $userId);
            })
            ->where('shops.company_id', '>', '1000')
            ->where('shops.company_id', '<', '7000')
            ->where('shops.company_id', 'LIKE', '%' . ($request->co_id) . '%')
            ->where('shops.area_id', 'LIKE', '%' . ($request->ar_id) . '%')
            ->where('shops.shop_name', 'LIKE', '%' . ($request->sh_name) . '%')
            ->select(
                'reports.id',
                'shops.company_id',
                'companies.co_name',
                'reports.shop_id',
                'shops.shop_name',
                'areas.area_name',
                'shops.shop_info',
                'reports.report',
                'reports.image1',
                'reports.created_at',
                'reports.updated_at',
                DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
                DB::raw("
                    CASE
                        WHEN SUM(CASE WHEN report_reads.user_id IS NULL THEN 1 ELSE 0 END) > 0
                        THEN NULL
                        ELSE MAX(report_reads.user_id)
                    END as report_reads
                "),
                DB::raw("
                    CASE
                        WHEN SUM(CASE WHEN comment_reads.user_id IS NULL THEN 1 ELSE 0 END) > 0
                        THEN NULL
                        ELSE MAX(comment_reads.user_id)
                    END as comment_reads
                ")
            )
            ->groupBy(
                'reports.id',
                'shops.company_id',
                'companies.co_name',
                'reports.shop_id',
                'shops.shop_name',
                'areas.area_name',
                'shops.shop_info',
                'reports.report',
                'reports.image1',
                'reports.created_at',
                'reports.updated_at'
            )
            ->orderBy('reports.updated_at', 'desc')
            ->paginate(100);

        $companies = Company::with('shop')
        // ->whereHas('shop',function($q){$q->where('is_selling','=',1);})
            ->where('id','>',1000)
            ->where('id','<>',1200)
            ->where('id','<>',1550)
            ->where('id','<>',3600)
            ->where('id','<>',2600)
            ->where('id','<',7000)->get();

        $areas = DB::table('areas')
            ->select(['areas.id','areas.area_name'])
            ->get();

        $shops = DB::table('shops')
            ->join('areas','areas.id','=','shops.area_id')
            ->join('companies','companies.id','=','shops.company_id')
            ->select('shops.id','shops.shop_name','shops.company_id','shops.area_id','areas.area_name','companies.co_name')
            ->where('shops.company_id','>','1000')
            ->where('shops.company_id','<','7000')
            ->where('shops.company_id','<>','1200')
            ->where('shops.company_id','<>','1550')
            ->where('shops.company_id','<>','2600')
            // ->where('shops.is_selling','=',1)
            ->where('shops.company_id','LIKE','%'.($request->co_id).'%')
            ->where('shops.area_id','LIKE','%'.($request->ar_id).'%')
            ->where('shops.shop_name','LIKE','%'.($request->sh_name).'%')
            ->paginate(100);
            // dd($reports);
            // dd($reports,$shops);

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'areas' => $areas,
            'shops' => $shops,
            'companies' => $companies,
            'filters' => $request->only(['co_id','ar_id','sh_name']),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // shops を絞り込み
    $shopsQuery = DB::table('shops')
    ->join('areas', 'areas.id', '=', 'shops.area_id')
    ->join('companies', 'companies.id', '=', 'shops.company_id')
    ->where('shops.is_selling',1)
    ->whereBetween('shops.company_id', [1000, 7000])
    ->whereNotIn('shops.company_id', [1200, 1550, 2600]);

        if ($request->co_id) {
            $shopsQuery->where('shops.company_id', $request->co_id);
        }
        if ($request->area_id) {
            $shopsQuery->where('shops.area_id', $request->area_id);
        }

        $shops = $shopsQuery
            ->select(['shops.id','shops.shop_name','shops.company_id','companies.co_name'])
            ->get();

        $companies = Company::whereHas('shop', fn($q)=> $q->where('is_selling',1))
            ->whereBetween('id',[1001,6999])
            ->whereNotIn('id',[1200,1550,2600,3600])
            ->get();

        $areas = DB::table('areas')->select(['id','area_name'])->get();

        return Inertia::render('Reports/Create', [
            'shops' => $shops,
            'companies' => $companies,
            'areas' => $areas,
            'filter' => [
                'co_id' => $request->co_id,
                'area_id' => $request->area_id,
            ],
        ]);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $login_user = User::findOrFail(Auth::id());
        $manager = new ImageManager(new GdDriver());

        /**
         * 画像共通処理
         * - EXIF補正
         * - 縦横比維持リサイズ
         * - 縦長なら width=500
         * - 横長/正方形なら max=400 に収める
         */
        $processImage = function ($file, $fileName) use ($manager) {

            $path = 'reports/' . $fileName;

            // 読み込み
            $img = $manager->read($file);

            // 向きを補正（EXIF）
            try {
                $img->orientate();
            } catch (Throwable $e) {}

            // 元サイズ
            $origW = $img->width();
            $origH = $img->height();

            // ===== 縦長画像（高さ > 幅）のときのみ 幅500px =====
            if ($origH > $origW) {
                $newW = 500;
                $newH = intval($origH * (500 / $origW));

            } else {
                // ===== 横長・正方形は 400px以内 =====
                $max = 400;

                if ($origW > $origH) {
                    // 横長
                    $newW = $max;
                    $newH = intval($origH * ($max / $origW));
                } else {
                    // 正方形
                    $newH = $max;
                    $newW = intval($origW * ($max / $origH));
                }
            }

            // リサイズ（縦横比維持）
            $img->resize($newW, $newH);

            // エンコード（JPEG）
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // 既存ファイルがあれば削除
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            // 保存
            Storage::put($path, (string)$encoded);

            return $fileName;
        };


        // ===============================
        // image1〜4 を共通処理で保存
        // ===============================

        $fileNameToStore1 = $request->file('image1')
            ? $processImage($request->file('image1'), $request->file('image1')->getClientOriginalName())
            : '';

        $fileNameToStore2 = $request->file('image2')
            ? $processImage($request->file('image2'), $request->file('image2')->getClientOriginalName())
            : '';

        $fileNameToStore3 = $request->file('image3')
            ? $processImage($request->file('image3'), $request->file('image3')->getClientOriginalName())
            : '';

        $fileNameToStore4 = $request->file('image4')
            ? $processImage($request->file('image4'), $request->file('image4')->getClientOriginalName())
            : '';


        // ===============================
        // DB 登録
        // ===============================
        $report = Report::create([
            'user_id' => $login_user->id,
            'shop_id' => $request->sh_id,
            'image1'  => $fileNameToStore1,
            'image2'  => $fileNameToStore2,
            'image3'  => $fileNameToStore3,
            'image4'  => $fileNameToStore4,
            'report'  => $request->report,
        ]);

        DB::table('report_reads')->upsert(
            [
                'report_id' => $report->id,
                'user_id'   => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            ['report_id', 'user_id']
        );

        return to_route('reports.index')
            ->with(['message' => 'Reportが登録されました', 'status' => 'info']);
    }
    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        $report=DB::table('reports')
            ->join('shops','shops.id','=','reports.shop_id')
            ->join('companies','companies.id','=','shops.company_id')
            ->join('areas','areas.id','=','shops.area_id')
            ->join('users','users.id','=','reports.user_id')
            ->select(['reports.id','reports.user_id','reports.user_id','users.name','shops.company_id','companies.co_name','reports.shop_id','shops.shop_name','areas.area_name','shops.shop_info','reports.report','reports.image1','reports.image2','reports.image3','reports.image4','reports.created_at'])
            ->where('reports.id',$report->id)
            ->first();

        $comment_exist = DB::table('comments')
            ->where('comments.report_id',$report->id)
            ->exists();

        $images = DB::table('reports')
            ->where('reports.id',$report->id)
            ->orderBy('reports.updated_at', 'desc')
            ->get();

        $login_user=Auth::id();

        $comments=DB::table('comments')
            ->join('users','users.id','=','comments.user_id')
            // 追記
            ->leftJoin('comment_reads', function ($join) use ($login_user) {
                $join->on('comments.id', '=', 'comment_reads.comment_id')
                    ->where('comment_reads.user_id', '=', $login_user);
            })
            ->where('comments.report_id',$report->id)
            ->select(['comments.id','comments.created_at','users.name','comments.comment','comment_reads.user_id as comment_reads'])
            ->orderBy('created_at','desc')
            ->get();

         // 既読処理
        DB::table('report_reads')->upsert(
            [
                'report_id' => $report->id,
                'user_id'   => User::findOrFail(Auth::id())->id,
                'created_at'   => now(),
                'updated_at' => now()

            ],
            ['report_id', 'user_id'], // uniqueキー
            ['updated_at']               // 更新するカラム

        );

        // dd($comment_exist);

        return Inertia::render('Reports/Show', [
            'comment_exist' => $comment_exist,
            'report' => $report,
            'images' => $images,
            'login_user' => $login_user,
            'comments' => $comments,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $report=DB::table('reports')
        ->join('users','users.id','=','reports.user_id')
        ->join('shops','shops.id','=','reports.shop_id')
        ->join('companies','companies.id','=','shops.company_id')
        ->select(['reports.id','companies.co_name','reports.shop_id','shops.shop_name','reports.user_id','users.name','reports.report','reports.image1','reports.image2','reports.image3','reports.image4','reports.created_at'])
        ->where('reports.id',$report->id)
        ->first();

        // dd($report);

        return Inertia::render('Reports/Edit', [
            'report' => $report,

        ]);
    }

    public function update(UpdateReportRequest $request, Report $report)
    {
        $manager = new ImageManager(new GdDriver());
        $folderName = 'reports';

        /**
         * 共通画像処理関数
         */
        $processImage = function ($file, $oldFileName = null) use ($manager, $folderName) {

            // 新しいファイル名
            $originalName = $file->getClientOriginalName();
            $fileNameToStore = $originalName;
            $filePath = "{$folderName}/{$fileNameToStore}";

            // 既存ファイル削除
            if ($oldFileName && Storage::exists("{$folderName}/{$oldFileName}")) {
                Storage::delete("{$folderName}/{$oldFileName}");
            }

            // 画像読み込み
            $img = $manager->read($file);

            // 向き補正
            try { $img->orientate(); } catch (Throwable $e) {}

            // 元サイズ取得
            $origW = $img->width();
            $origH = $img->height();

            // === 縦長なら幅500にリサイズ ===
            if ($origH > $origW) {
                $newW = 500;
                $newH = intval($origH * (500 / $origW));
            } else {
                // === 横長・正方形は 400px 基準 ===
                $max = 400;

                if ($origW >= $origH) {
                    // 横長：幅400
                    $newW = $max;
                    $newH = intval($origH * ($max / $origW));
                } else {
                    // 正方形に近い縦：高さ400
                    $newH = $max;
                    $newW = intval($origW * ($max / $origH));
                }
            }

            // リサイズ
            $img->resize($newW, $newH);

            // JPEGエンコード
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // 保存
            Storage::put($filePath, (string)$encoded);

            return $fileNameToStore;
        };


        // ====== image1 ======
        $fileNameToStore1 = $report->image1;
        if ($request->hasFile('image1')) {
            $fileNameToStore1 = $processImage($request->file('image1'), $report->image1);
        }

        // ====== image2 ======
        $fileNameToStore2 = $report->image2;
        if ($request->hasFile('image2')) {
            $fileNameToStore2 = $processImage($request->file('image2'), $report->image2);
        }

        // ====== image3 ======
        $fileNameToStore3 = $report->image3;
        if ($request->hasFile('image3')) {
            $fileNameToStore3 = $processImage($request->file('image3'), $report->image3);
        }

        // ====== image4 ======
        $fileNameToStore4 = $report->image4;
        if ($request->hasFile('image4')) {
            $fileNameToStore4 = $processImage($request->file('image4'), $report->image4);
        }


        // === レコード更新 ===
        $report->update([
            'shop_id' => $request->shop_id,
            'report'  => $request->report,
            'image1'  => $fileNameToStore1,
            'image2'  => $fileNameToStore2,
            'image3'  => $fileNameToStore3,
            'image4'  => $fileNameToStore4,
        ]);

        return to_route('reports.show', ['report' => $report->id])
            ->with(['message' => '更新されました', 'status' => 'info']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $report = Report::findOrFail($report->id);

        $filePath1 = 'reports/' . $report->image1;
        if((Storage::exists($filePath1))){
            Storage::delete($filePath1);
            // dd($filePath1,$request->photo1);
        }

        $filePath2 = 'reports/' . $report->image2;
        if((Storage::exists($filePath2))){
            Storage::delete($filePath2);
            // dd($filePath1,$request->photo1);
        }

        $filePath3 = 'reports/' . $report->image3;
        if((Storage::exists($filePath3))){
            Storage::delete($filePath3);
            // dd($filePath1,$request->photo1);
        }

        $filePath4 = 'reports/' . $report->image4;
        if((Storage::exists($filePath4))){
            Storage::delete($filePath4);
            // dd($filePath1,$request->photo1);
        }

        Report::findOrFail($report->id)->delete();

        return to_route('reports.index', ['id' => $report->detail_id])->with(['message' => '削除されました', 'status' => 'alert']);

    }

    public function del_image1(Request $request)
    {
        // $reportはルートモデルバインディングで渡されているので、findOrFailは不要
        // $request->id は不要
        // dd($request->report);

        $target_report = Report::findOrFail($request->report);

        // dd($target_report);

        $filePath = 'reports/' . $target_report->image1;

        // dd($filePath);

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $target_report->image1 = null;  // '' より null のほうがベター
        $target_report->save();

        return to_route('reports.show', ['report' => $request->report])
            ->with(['message' => '削除されました', 'status' => 'alert']);
    }

    public function del_image2(Request $request)
    {
        $target_report = Report::findOrFail($request->report);

        $filePath = 'reports/' . $target_report->image2;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $target_report->image2 = null;
        $target_report->save();

        return to_route('reports.show', ['report' => $request->report])
            ->with(['message' => '削除されました', 'status' => 'alert']);
    }

    public function del_image3(Request $request)
    {
        $target_report = Report::findOrFail($request->report);

        $filePath = 'reports/' . $target_report->image3;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $target_report->image3 = null;
        $target_report->save();

        return to_route('reports.show', ['report' => $request->report])
            ->with(['message' => '削除されました', 'status' => 'alert']);
    }

    public function del_image4(Request $request)
    {
        $target_report = Report::findOrFail($request->report);

        $filePath = 'reports/' . $target_report->image4;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $target_report->image4 = null;
        $target_report->save();

        return to_route('reports.show', ['report' => $request->report])
            ->with(['message' => '削除されました', 'status' => 'alert']);
    }

}
