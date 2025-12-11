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
// use Intervention\Image\Imagick\ImagickDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Interfaces\ImageInterface;
// use Intervention\Image\Gd\GdDriver;


class ReportController extends Controller
{

   /**
     * HEIC/HEIF → JPEG 変換
     */
    private function convertHeicToJpg(UploadedFile $file): string
    {
        $path = $file->getRealPath();
        $imagick = new \Imagick();
        $imagick->readImage($path);
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompressionQuality(90);

        $tmpPath = tempnam(sys_get_temp_dir(), 'jpg_') . '.jpg';
        $imagick->writeImage($tmpPath);
        $imagick->clear();
        $imagick->destroy();

        return $tmpPath;
    }

    /**
     * EXIF Orientation を見てピクセル自体を回転
     */
    private function fixOrientation(ImageInterface $img): ImageInterface
    {
        try {
            $orientation = $img->exif('Orientation');

            if ($orientation) {
                switch ($orientation) {
                    case 3:
                        $img->rotate(180);
                        break;
                    case 6:
                        $img->rotate(90);
                        break;
                    case 8:
                        $img->rotate(-90);
                        break;
                }
                // Orientation フラグをリセット
                if (method_exists($img->getCore(), 'setImageOrientation')) {
                    $img->getCore()->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
                }
            }
        } catch (\Throwable $e) {
            // EXIFがなくても無視
        }
        return $img;
    }

    /**
     * 共通画像処理（スマホ完全対応）
     */
    private function processImage(UploadedFile $file, string $fileName): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // HEIC → JPEG
        if ($extension === 'heic' || $extension === 'heif') {
            $filePath = $this->convertHeicToJpg($file);
        } else {
            $filePath = $file->getRealPath();
        }

        // Imagick ドライバで ImageManager 作成
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());

        // v3では make() ではなく read()
        $img = $manager->read($filePath);

        // EXIF補正
        $img = $this->fixOrientation($img);

        $origW = $img->width();
        $origH = $img->height();

        // 長辺 400px
        if ($origW >= $origH) {
            $newW = 400;
            $newH = intval($origH * ($newW / $origW));
        } else {
            $newH = 400;
            $newW = intval($origW * ($newH / $origH));
        }

        $img->resize($newW, $newH);

        $path = "reports/" . $fileName;

        if (\Illuminate\Support\Facades\Storage::exists($path)) {
            \Illuminate\Support\Facades\Storage::delete($path);
        }

        // v3では JpegEncoder を使用
        $encoder = new JpegEncoder(quality: 90);
        $encoded = $img->encode($encoder);

        \Illuminate\Support\Facades\Storage::put($path, (string)$encoded);

        return $fileName;
    }
    /**
     * store
     */
    public function store(StoreReportRequest $request)
    {
        $login_user = Auth::user();

        $imageFields = ['image1', 'image2', 'image3', 'image4'];
        $saved = [];

        foreach ($imageFields as $field) {
            if ($request->file($field)) {
                $saved[$field] = $this->processImage(
                    $request->file($field),
                    uniqid() . '_' . $request->file($field)->getClientOriginalName()
                );
            } else {
                $saved[$field] = '';
            }
        }

        $report = Report::create([
            'user_id' => $login_user->id,
            'shop_id' => $request->sh_id,
            'report'  => $request->report,
            'image1'  => $saved['image1'],
            'image2'  => $saved['image2'],
            'image3'  => $saved['image3'],
            'image4'  => $saved['image4'],
        ]);

        DB::table('report_reads')->upsert([
            'report_id' => $report->id,
            'user_id'   => $login_user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], ['report_id', 'user_id']);

        return to_route('reports.index')
            ->with(['message' => 'Reportが登録されました', 'status' => 'info']);
    }



/**
     * update
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        $imageFields = ['image1', 'image2', 'image3', 'image4'];

        foreach ($imageFields as $field) {
            if ($request->file($field)) {
                $newName = $this->processImage(
                    $request->file($field),
                    uniqid() . '_' . $request->file($field)->getClientOriginalName()
                );

                if ($report->$field) {
                    $oldPath = "reports/" . $report->$field;
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                $report->$field = $newName;
            }
        }

        $report->report = $request->report;
        $report->save();

        return to_route('reports.index')
            ->with(['message' => 'Reportが更新されました', 'status' => 'info']);
    }

}
