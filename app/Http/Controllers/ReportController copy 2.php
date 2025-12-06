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
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Inertia\Inertia;
use Throwable; // 追加


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $login_user = User::findOrFail(Auth::id());
        // GDドライバーでマネージャを生成
        $manager = new ImageManager(new GdDriver());

        $folderName = 'reports';

       // image1の処理
        if (!is_null($request->file('image1'))) {
            $file = $request->file('image1');
            $originalName1 = $file->getClientOriginalName();
            $basename1 = pathinfo($originalName1, PATHINFO_FILENAME);
            $fileNameToStore1 = $originalName1;
            $filePath1 = 'reports/' . $fileNameToStore1;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image1', $fileNameToStore1)->exists();
            if ($isExist) {
                Storage::delete($filePath1);
            }
            Storage::put($filePath1, (string)$encoded);
        } else {
            $fileNameToStore1 = '';
        }

        // image2の処理
        if (!is_null($request->file('image2'))) {
            $file = $request->file('image2');
            $originalName2 = $file->getClientOriginalName();
            $basename2 = pathinfo($originalName2, PATHINFO_FILENAME);
            $fileNameToStore2 = $originalName2;
            $filePath2 = 'reports/' . $fileNameToStore2;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image2', $fileNameToStore2)->exists();
            if ($isExist) {
                Storage::delete($filePath2);
            }
            Storage::put($filePath2, (string)$encoded);
        } else {
            $fileNameToStore2 = '';
        }


        // image3の処理
        if (!is_null($request->file('image3'))) {
            $file = $request->file('image3');
            $originalName3 = $file->getClientOriginalName();
            $basename3 = pathinfo($originalName3, PATHINFO_FILENAME);
            $fileNameToStore3 = $originalName3;
            $filePath3 = 'reports/' . $fileNameToStore3;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image3', $fileNameToStore3)->exists();
            if ($isExist) {
                Storage::delete($filePath3);
            }
            Storage::put($filePath3, (string)$encoded);
        } else {
            $fileNameToStore3 = '';
        }

        // image4の処理
        if (!is_null($request->file('image4'))) {
            $file = $request->file('image4');
            $originalName4 = $file->getClientOriginalName();
            $basename4 = pathinfo($originalName4, PATHINFO_FILENAME);
            $fileNameToStore4 = $originalName4;
            $filePath4 = 'reports/' . $fileNameToStore4;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image4', $fileNameToStore4)->exists();
            if ($isExist) {
                Storage::delete($filePath4);
            }
            Storage::put($filePath4, (string)$encoded);
        } else {
            $fileNameToStore4 = '';
        }



        // dd($request,$request->co_id,$request->sh_id,$request->report,$request->image1);
        $report=Report::create([
            'user_id' => $login_user->id,
            'shop_id' => $request->sh_id,
            'image1' => $fileNameToStore1,
            'image2' => $fileNameToStore2,
            'image3' => $fileNameToStore3,
            'image4' => $fileNameToStore4,
            'report' => $request->report,
        ]);

        DB::table('report_reads')->upsert(
            [
                'report_id' => $report->id,
                'user_id'   => User::findOrFail(Auth::id())->id,
                'created_at'   => now(),
                'updated_at' => now()

            ],
            ['report_id', 'user_id'], // uniqueキー
            // ['created_at','updated_at']               // 更新するカラム

        );



        return to_route('reports.index')->with(['message'=>'Reportが登録されました','status'=>'info']);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {

        $login_user = User::findOrFail(Auth::id());
        // GDドライバーでマネージャを生成
        $manager = new ImageManager(new GdDriver());
        $folderName = 'reports';

        // image1の処理
        $filePath1 = 'reports/' . $report->image1;
        if(!is_null($request->file('image1')) && (Storage::exists($filePath1))){
            Storage::delete($filePath1);
            // Storage::disk('public')->delete($folderName . '/' . $filename);
        }

        if (!is_null($request->file('image1'))) {
            $file = $request->file('image1');
            $originalName1 = $file->getClientOriginalName();
            $basename1 = pathinfo($originalName1, PATHINFO_FILENAME);
            $fileNameToStore1 = $originalName1;
            $filePath1 = 'reports/' . $fileNameToStore1;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image1', $fileNameToStore1)->exists();
            if ($isExist) {
                Storage::delete($filePath1);
            }
            Storage::put($filePath1, (string)$encoded);
        } else {
            $fileNameToStore1 = '';
        }

        // image2の処理
        $filePath2 = 'reports/' . $report->image2;
        if(!is_null($request->file('image2')) && (Storage::exists($filePath2))){
            Storage::delete($filePath2);
            // Storage::disk('public')->delete($folderName . '/' . $filename);
        }

        if (!is_null($request->file('image2'))) {
            $file = $request->file('image2');
            $originalName2 = $file->getClientOriginalName();
            $basename2 = pathinfo($originalName2, PATHINFO_FILENAME);
            $fileNameToStore2 = $originalName2;
            $filePath2 = 'reports/' . $fileNameToStore2;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image2', $fileNameToStore2)->exists();
            if ($isExist) {
                Storage::delete($filePath2);
            }
            Storage::put($filePath2, (string)$encoded);
        } else {
            $fileNameToStore2 = '';
        }

        // image3の処理
        $filePath3 = 'reports/' . $report->image3;
        if(!is_null($request->file('image3')) && (Storage::exists($filePath3))){
            Storage::delete($filePath3);
            // Storage::disk('public')->delete($folderName . '/' . $filename);
        }

        if (!is_null($request->file('image3'))) {
            $file = $request->file('image3');
            $originalName3 = $file->getClientOriginalName();
            $basename3 = pathinfo($originalName3, PATHINFO_FILENAME);
            $fileNameToStore3 = $originalName3;
            $filePath3 = 'reports/' . $fileNameToStore3;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image3', $fileNameToStore3)->exists();
            if ($isExist) {
                Storage::delete($filePath3);
            }
            Storage::put($filePath3, (string)$encoded);
        } else {
            $fileNameToStore3 = '';
        }

        // image4の処理
        $filePath4 = 'reports/' . $report->image4;
        if(!is_null($request->file('image4')) && (Storage::exists($filePath4))){
            Storage::delete($filePath4);
            // Storage::disk('public')->delete($folderName . '/' . $filename);
        }
        if (!is_null($request->file('image4'))) {
            $file = $request->file('image4');
            $originalName4 = $file->getClientOriginalName();
            $basename4 = pathinfo($originalName4, PATHINFO_FILENAME);
            $fileNameToStore4 = $originalName4;
            $filePath4 = 'reports/' . $fileNameToStore4;

            // === 画像読み込み ===
            $img = $manager->read($file);

            // === 向き補正（Exifが有効なら実行）===
            try {
                $img->orientate();
            } catch (Throwable $e) {
                // orientate未対応環境ではスキップ
            }

            // === サイズ調整 ===
            $img->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ✅ v3対応: エンコードには Encoder クラスを使用
            $encoded = $img->encode(new JpegEncoder(quality: 90));

            // === 保存処理 ===
            $isExist = Report::where('image4', $fileNameToStore4)->exists();
            if ($isExist) {
                Storage::delete($filePath4);
            }
            Storage::put($filePath4, (string)$encoded);
        } else {
            $fileNameToStore4 = '';
        }

        $report = Report::findOrFail($request->id);
        $report->shop_id = $request->shop_id;
        $report->report = $request->report;
        $report->image1 = $fileNameToStore1;
        $report->image2 = $fileNameToStore2;
        $report->image3 = $fileNameToStore3;
        $report->image4 = $fileNameToStore4;
        $report->save();


        // return to_route('reports.index2', ['id' => $request->detail_id])->with([
        //     'message' => 'Reportが更新されました',
        //     'status' => 'info',
        // ]);

        return to_route('reports.show', ['report' => $request->id])
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



