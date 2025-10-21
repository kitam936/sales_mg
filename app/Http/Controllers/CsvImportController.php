<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CsvImportController extends Controller
{
    public function index()
    {
        return inertia('CsvImport/Index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();

        // 全行数（ヘッダー除く）
        $lineCount = max(0, count(file($path)) - 1);

        // 進捗ファイル初期化
        Storage::put('progress.json', json_encode([
            'total' => $lineCount,
            'current' => 0
        ]));

        DB::transaction(function () use ($path) {
            $handle = fopen($path, 'r');

            // ヘッダー行読み飛ばし
            $header = fgetcsv($handle);
            if ($header) {
                $header = array_map(fn($v) => mb_convert_encoding($v, 'UTF-8', 'SJIS-win'), $header);
            }

            $batchSize = 1000;
            $rows = [];
            $processed = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // 文字コード変換
                $row = array_map(fn($v) => mb_convert_encoding($v, 'UTF-8', 'SJIS-win'), $row);

                $rows[] = [
                    'id'            => $row[0] ?? null,
                    'shop_name'     => $row[1] ?? null,
                    'shop_info'     => $row[2] ?? null,
                    'company_id'    => $row[3] ?? null,
                    'shop_postcode' => $row[4] ?? null,
                    'shop_address'  => $row[5] ?? null,
                    'shop_tel'      => $row[6] ?? null,
                    'rate'          => $row[7] ?? null,
                    'is_selling'    => $row[8] ?? null,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];

                $processed++;

                // バッチ単位でupsert
                if (count($rows) >= $batchSize) {
                    DB::table('shops')->upsert(
                        $rows,
                        ['id'], // 主キーで更新判定
                        [
                            'shop_name', 'shop_info', 'company_id',
                            'shop_postcode', 'shop_address', 'shop_tel',
                            'rate', 'is_selling', 'updated_at'
                        ]
                    );
                    $rows = [];

                    // 進捗更新
                    Storage::put('progress.json', json_encode([
                        'total' => (int) json_decode(Storage::get('progress.json'), true)['total'],
                        'current' => $processed
                    ]));
                }
            }

            // 残り処理
            if (!empty($rows)) {
                DB::table('shops')->upsert(
                    $rows,
                    ['id'],
                    [
                        'shop_name', 'shop_info', 'company_id',
                        'shop_postcode', 'shop_address', 'shop_tel',
                        'rate', 'is_selling', 'updated_at'
                    ]
                );
                Storage::put('progress.json', json_encode([
                    'total' => (int) json_decode(Storage::get('progress.json'), true)['total'],
                    'current' => $processed
                ]));
            }

            fclose($handle);
        });

        return redirect()->route('csv.import.index')
        ->with(['message'=>'csvをインポートしました','status'=>'info']);
        // ->with('success', 'CSVをインポートしました（shopsテーブル）');
    }

    public function progress()
    {
        if (Storage::exists('progress.json')) {
            return response()->json(json_decode(Storage::get('progress.json'), true));
        }
        return response()->json(['total' => 0, 'current' => 0]);
    }
}
