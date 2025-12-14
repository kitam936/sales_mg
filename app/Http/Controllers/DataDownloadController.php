<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Jobs\SendOrderResponseMail;
use App\Models\Report;
use App\Models\User;
use App\Models\Order;
use Response;

class DataDownloadController extends Controller
{
    public function Report_DL(Request $request)
    {
        $reports = DB::table('reports')->get();


        // dd($request,$stints);

        return view('stints.my_stint_csv_dl',compact('tire_temps','temps','road_temps','humis','cirs','karts','tires','engines'));

    }
    public function ReportCSV_download(Request $request)
    {
        $reports = Report::where('id', '>=',0)
        ->select('reports.id','reports.shop_id','reports.image1','reports.image2','reports.image3','reports.image4',
                'reports.comment','reports.created_at','reports.updated_at','reports.user_id')
        ->orderby('reports.id','asc')
        ->get();

        // dd($stints);
        $csvHeader = [
            'reports.id','reports.shop_id','reports.image1','reports.image2','reports.image3','reports.image4',
            'reports.comment','reports.created_at','reports.updated_at','reports.user_id'];

        $csvData = $reports->toArray();

        // dd($request,$csvHeader,$csvData);

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                mb_convert_variables('sjis-win','utf-8',$row);
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="my_stints.csv"',
        ]);

        return $response;

    }

    public function manual_download()
    {
        $relative = 'manual/manual.pdf';

        // 正しいフルパスを手動で作る
        $fullPath = storage_path('app\\public\\' . str_replace('/', '\\', $relative));

        if (!file_exists($fullPath)) {
            dd('NOT FOUND PATH = ' . $fullPath);
            abort(404, 'File not found.');
        }

        return response()->download($fullPath, 'manual.pdf');
    }


    public function HinbanImageCheck_CSV_download()
    {

        $images = DB::table('hinbans')
        ->join('units','units.id','=','hinbans.unit_id')
        ->leftjoin('images','hinbans.id','images.hinban_id')
        ->where('hinbans.vendor_id','<>',8200)
        ->where('hinbans.year_code','>=',25)
        ->whereNull('images.hinban_id')
        ->select('hinbans.unit_id as unit','hinbans.id as hinban','hinbans.hinban_name')
        ->get();


        // dd($stints);
        $csvHeader = [
            'unit','hinban','hinmei'];

        $csvData = $images->toArray();

        // dd($request,$csvHeader,$csvData);

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                $row = (array)$row; // 必要に応じてオブジェクトを配列に変換
                mb_convert_variables('sjis-win', 'utf-8', $row);
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="HinbanImageCheck.csv"');

        return $response;

    }

    public function SkuImageCheck_CSV_download()
    {
        $sku_images = DB::table('skus')
        ->leftjoin('sku_images','skus.id','sku_images.sku_id')
        ->join('sizes','skus.size_id','sizes.id')
        ->join('hinbans','hinbans.id','skus.hinban_id')
        ->join('units','units.id','=','hinbans.unit_id')
        ->where('hinbans.vendor_id','<>',8200)
        ->whereNull('sku_images.sku_id')
        ->where('skus.col_id','<>',99)
        ->where('hinbans.year_code','>=',25)
        ->select('hinbans.unit_id as unit','hinbans.id as hinban','skus.col_id','skus.size_id','hinbans.hinban_name')
        ->get();


        // dd($stints);
        $csvHeader = [
            'unit','hinban','col','size','hinmei'];

        $csvData = $sku_images->toArray();

        // dd($request,$csvHeader,$csvData);

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                $row = (array)$row; // 必要に応じてオブジェクトを配列に変換
                mb_convert_variables('sjis-win', 'utf-8', $row);
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="SkuImageCheck.csv"');

        return $response;

    }





}
