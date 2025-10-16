<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $reports = DB::table('reports')
            ->join('order_details', 'reports.detail_id', '=', 'order_details.id')
            ->join('users', 'reports.staff_id', '=', 'users.id')
            ->leftJoin('comments', 'comments.report_id', '=', 'reports.id')
            ->where('order_details.id', $id)
            ->groupBy('reports.id','reports.detail_id', 'users.name', 'reports.title', 'reports.report', 'reports.created_at')
            ->selectRaw('reports.id as report_id,reports.detail_id, users.name as staff_name, reports.title, reports.report, count(comments.id) as comment_cnt, reports.created_at')
            ->orderBy('reports.created_at', 'desc')
            ->get();

        $detail = DB::table('order_details')
            ->join('detail_statuses', 'order_details.detail_status', '=', 'detail_statuses.id')
            ->where('order_details.id', $id)
            ->select(['order_details.id', 'order_details.order_id','detail_statuses.detail_status_name','detail_info','order_details.created_at'])
            ->first();

        // dd($reports, $detail);

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'detail' => $detail,
            'id' => $id,
        ]);

    }

    public function my_index($id)
    {
        $reports = DB::table('reports')
            ->join('order_details', 'reports.detail_id', '=', 'order_details.id')
            ->join('users', 'reports.staff_id', '=', 'users.id')
            ->leftJoin('comments', 'comments.report_id', '=', 'reports.id')
            ->where('order_details.id', $id)
            ->groupBy('reports.id','reports.detail_id', 'users.name', 'reports.title', 'reports.report', 'reports.created_at')
            ->selectRaw('reports.id as report_id,reports.detail_id, users.name as staff_name, reports.title, reports.report, count(comments.id) as comment_cnt, reports.created_at')
            ->orderBy('reports.created_at', 'desc')
            ->get();

        $detail = DB::table('order_details')
            ->join('detail_statuses', 'order_details.detail_status', '=', 'detail_statuses.id')
            ->where('order_details.id', $id)
            ->select(['order_details.id', 'order_details.order_id','detail_statuses.detail_status_name','detail_info','order_details.created_at'])
            ->first();

        // dd($reports, $detail);

        return Inertia::render('MyOrders/MyReport_Index', [
            'reports' => $reports,
            'detail' => $detail,
            'id' => $id,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $detail = DB::table('order_details')
        ->where('order_details.id',$id)
        ->first();

        // dd($detail);

        return Inertia::render('Reports/Create', [
            'id' => $detail->id,
            'detail' => $detail,
            'old' => session()->getOldInput(),
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $folderName = 'reports';

        // dd($request->all());

        if (!is_null($request->file('image1'))) {
            $fileName1 = uniqid(rand() . '_');
            $extension1 = $request->file('image1')->extension();
            $fileNameToStore1 = $fileName1 . '.' . $extension1;

            $filePath1 = $request->file('image1')->getPathname();

            // ImageManagerインスタンスを作成（GD使用）
            $manager1 = ImageManager::withDriver(GdDriver::class); // ✅ 正しい
            $image1 = $manager1->read($filePath1);

            // Exif情報で向きを補正（GD + JPEGのみ）
            $exif11 = @exif_read_data($filePath1);
            if (!empty($exif11['Orientation'])) {
                switch ($exif11['Orientation']) {
                    case 3:
                        $image1 = $image1->rotate(180);
                        break;
                    case 6:
                        $image1 = $image1->rotate(90);
                        break;
                    case 8:
                        $image1 = $image1->rotate(-90);
                        break;
                }
            }

            // リサイズ（縦横比維持）
            $resizedImage1 = $image1->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode(); // デフォルトで元のフォーマットでエンコード

            // 保存
            Storage::put($folderName . '/' . $fileNameToStore1, $resizedImage1);
        } else {
            $fileNameToStore1 = '';
        }

        // 画像2の処理
        if (!is_null($request->file('image2'))) {
            $fileName2 = uniqid(rand() . '_');
            $extension2 = $request->file('image2')->extension();
            $fileNameToStore2 = $fileName2 . '.' . $extension2;

            $filePath2 = $request->file('image2')->getPathname();
            $manager2 = ImageManager::withDriver(GdDriver::class);
            $image2 = $manager2->read($filePath2);
            $exif12 = @exif_read_data($filePath2);
            if (!empty($exif12['Orientation'])) {
                switch ($exif12['Orientation']) {
                    case 3:
                        $image2 = $image2->rotate(180);
                        break;
                    case 6:
                        $image2 = $image2->rotate(90);
                        break;
                    case 8:
                        $image2 = $image2->rotate(-90);
                        break;
                }
            }
            $resizedImage2 = $image2->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore2, $resizedImage2);
        } else {
            $fileNameToStore2 = '';
        }

        // 画像3の処理
        if (!is_null($request->file('image3'))) {
            $fileName3 = uniqid(rand() . '_');
            $extension3 = $request->file('image3')->extension();
            $fileNameToStore3 = $fileName3 . '.' . $extension3;

            $filePath3 = $request->file('image3')->getPathname();
            $manager3 = ImageManager::withDriver(GdDriver::class);
            $image3 = $manager3->read($filePath3);
            $exif13 = @exif_read_data($filePath3);
            if (!empty($exif13['Orientation'])) {
                switch ($exif13['Orientation']) {
                    case 3:
                        $image3 = $image3->rotate(180);
                        break;
                    case 6:
                        $image3 = $image3->rotate(90);
                        break;
                    case 8:
                        $image3 = $image3->rotate(-90);
                        break;
                }
            }
            $resizedImage3 = $image3->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore3, $resizedImage3);
        } else {
            $fileNameToStore3 = '';
        }

        // 画像4の処理
        if (!is_null($request->file('image4'))) {
            $fileName4 = uniqid(rand() . '_');
            $extension4 = $request->file('image4')->extension();
            $fileNameToStore4 = $fileName4 . '.' . $extension4;

            $filePath4 = $request->file('image4')->getPathname();
            $manager4 = ImageManager::withDriver(GdDriver::class);
            $image4 = $manager4->read($filePath4);
            $exif14 = @exif_read_data($filePath4);
            if (!empty($exif14['Orientation'])) {
                switch ($exif14['Orientation']) {
                    case 3:
                        $image4 = $image4->rotate(180);
                        break;
                    case 6:
                        $image4 = $image4->rotate(90);
                        break;
                    case 8:
                        $image4 = $image4->rotate(-90);
                        break;
                }
            }
            $resizedImage4 = $image4->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore4, $resizedImage4);
        } else {
            $fileNameToStore4 = '';
        }


        $report=Report::create([
            'detail_id' => $request->detail_id,
            'staff_id' => Auth::user()->id,
            'title' => $request->title,
            'report' => $request->report,
            'image1' => $fileNameToStore1,
            'image2' => $fileNameToStore2,
            'image3' => $fileNameToStore3,
            'image4' => $fileNameToStore4,
        ]);

        $report_data = Report::where('id', $report->id)
            ->first();

        return to_route('reports.index2', ['id' => $report_data->detail_id])->with([
            'message' => 'Reportが登録されました',
            'status' => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {

        $report=DB::table('reports')
        ->join('users','users.id','=','reports.staff_id')
        ->select(['reports.id','reports.detail_id','reports.staff_id','users.name','reports.title','reports.report','reports.image1','reports.image2','reports.image3','reports.image4','reports.created_at'])
        ->where('reports.id',$report->id)
        ->first();

        $comments = DB::table('comments')
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->select(['comments.id', 'comments.report_id', 'users.name', 'comments.comment', 'comments.created_at'])
            ->where('comments.report_id', $report->id)
            ->orderBy('comments.created_at', 'desc')
            ->get();


        // dd($report);

        return Inertia::render('Reports/Show', [
            'report' => $report,
            'comments' => $comments,
            'login_user' => Auth::id(), // 現在のユーザーIDを渡す
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $report=DB::table('reports')
        ->join('users','users.id','=','reports.staff_id')
        ->select(['reports.id','reports.detail_id','reports.staff_id','users.name','reports.title','reports.report','reports.image1','reports.image2','reports.image3','reports.image4','reports.created_at'])
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
        // dd($request->all());
        $folderName = 'reports';

        $filePath1 = 'reports/' . $report->image1;

        // $is_exists = Storage::exists($filePath1);

        // dd($filePath1,$request->file('image1'),$is_exists);

        if(!is_null($request->file('image1')) && (Storage::exists($filePath1))){
            Storage::delete($filePath1);
            // Storage::disk('public')->delete($folderName . '/' . $filename);
        }

        if (!is_null($request->file('image1'))) {
            $fileName1 = uniqid(rand() . '_');
            $extension1 = $request->file('image1')->extension();
            $fileNameToStore1 = $fileName1 . '.' . $extension1;

            $filePath1 = $request->file('image1')->getPathname();

            // ImageManagerインスタンスを作成（GD使用）
            $manager1 = ImageManager::withDriver(GdDriver::class); // ✅ 正しい
            $image1 = $manager1->read($filePath1);

            // Exif情報で向きを補正（GD + JPEGのみ）
            $exif1 = @exif_read_data($filePath1);
            if (!empty($exif1['Orientation'])) {
                switch ($exif1['Orientation']) {
                    case 3:
                        $image1 = $image1->rotate(180);
                        break;
                    case 6:
                        $image1 = $image1->rotate(90);
                        break;
                    case 8:
                        $image1 = $image1->rotate(-90);
                        break;
                }
            }

            // リサイズ（縦横比維持）
            $resizedImage1 = $image1->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode(); // デフォルトで元のフォーマットでエンコード

            // 保存
            Storage::put($folderName . '/' . $fileNameToStore1, $resizedImage1);
            // Storage::disk('public')->put($folderName . '/' . $fileNameToStore, $resizedImage);
        } else {
            $fileNameToStore1 = $report->image1;
        }

        // 画像2
        $filePath2 = 'reports/' . $report->image2;

        if(!is_null($request->file('image2')) && (Storage::exists($filePath2))){
            Storage::delete($filePath2);
        }

        if (!is_null($request->file('image2'))) {
            $fileName2 = uniqid(rand() . '_');
            $extension2 = $request->file('image2')->extension();
            $fileNameToStore2 = $fileName2 . '.' . $extension2;

            $filePath2 = $request->file('image2')->getPathname();
            $manager2 = ImageManager::withDriver(GdDriver::class);
            $image2 = $manager2->read($filePath2);
            $exif2 = @exif_read_data($filePath2);
            if (!empty($exif2['Orientation'])) {
                switch ($exif2['Orientation']) {
                    case 3:
                        $image2 = $image2->rotate(180);
                        break;
                    case 6:
                        $image2 = $image2->rotate(90);
                        break;
                    case 8:
                        $image2 = $image2->rotate(-90);
                        break;
                }
            }
            $resizedImage2 = $image2->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore2, $resizedImage2);
        } else {
            $fileNameToStore2 = $report->image2;
        }

        // 画像3
        $filePath3 = 'reports/' . $report->image3;

        if(!is_null($request->file('image3')) && (Storage::exists($filePath3))){
            Storage::delete($filePath3);
        }

        if (!is_null($request->file('image3'))) {
            $fileName3 = uniqid(rand() . '_');
            $extension3 = $request->file('image3')->extension();
            $fileNameToStore3 = $fileName3 . '.' . $extension3;

            $filePath3 = $request->file('image3')->getPathname();
            $manager3 = ImageManager::withDriver(GdDriver::class);
            $image3 = $manager3->read($filePath3);
            $exif3 = @exif_read_data($filePath3);
            if (!empty($exif3['Orientation'])) {
                switch ($exif3['Orientation']) {
                    case 3:
                        $image3 = $image3->rotate(180);
                        break;
                    case 6:
                        $image3 = $image3->rotate(90);
                        break;
                    case 8:
                        $image3 = $image3->rotate(-90);
                        break;
                }
            }
            $resizedImage3 = $image3->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore3, $resizedImage3);
        } else {
            $fileNameToStore3 = $report->image3;
        }

        // 画像4
        $filePath4 = 'reports/' . $report->image4;

        if(!is_null($request->file('image4')) && (Storage::exists($filePath4))){
            Storage::delete($filePath4);
        }

        if (!is_null($request->file('image4'))) {
            $fileName4 = uniqid(rand() . '_');
            $extension4 = $request->file('image4')->extension();
            $fileNameToStore4 = $fileName4 . '.' . $extension4;

            $filePath4 = $request->file('image4')->getPathname();
            $manager4 = ImageManager::withDriver(GdDriver::class);
            $image4 = $manager4->read($filePath4);
            $exif4 = @exif_read_data($filePath4);
            if (!empty($exif4['Orientation'])) {
                switch ($exif4['Orientation']) {
                    case 3:
                        $image4 = $image4->rotate(180);
                        break;
                    case 6:
                        $image4 = $image4->rotate(90);
                        break;
                    case 8:
                        $image4 = $image4->rotate(-90);
                        break;
                }
            }
            $resizedImage4 = $image4->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            Storage::put($folderName . '/' . $fileNameToStore4, $resizedImage4);
        } else {
            $fileNameToStore4 = $report->image4;
        }



        $report = Report::findOrFail($request->id);
        $report->detail_id = $request->detail_id;
        $report->title = $request->title;
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

        return to_route('reports.show2', ['report' => $request->id])
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

        return to_route('reports.index2', ['id' => $report->detail_id])->with(['message' => '削除されました', 'status' => 'alert']);

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

        return to_route('reports.show2', ['report' => $request->report])
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

        return to_route('reports.show2', ['report' => $request->report])
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

        return to_route('reports.show2', ['report' => $request->report])
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

        return to_route('reports.show2', ['report' => $request->report])
            ->with(['message' => '削除されました', 'status' => 'alert']);
    }

}

