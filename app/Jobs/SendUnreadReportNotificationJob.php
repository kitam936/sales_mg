<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UnreadReportMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUnreadReportNotificationJob
{
    public function handle(): void
    {
        // Log::info('📨 SendUnreadReportNotificationJob 起動: ' . now());

        // mailService=true で、未読報告書があるユーザーを取得
        $users = User::where('mailService', true)
            ->where(function($q) {
            $q->where('dept_id', '<=', 3);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reports')
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('report_reads')
                            ->whereColumn('report_reads.report_id', 'reports.id')
                            ->whereColumn('report_reads.user_id', 'users.id');
                    });
            })
            ->get();

            // dd($users);

        if ($users->isEmpty()) {
            Log::info('未読店頭Reportの該当者なし: ' . now());
            return;
        }

        foreach ($users as $user) {
            Mail::to($user->email)->send(new UnreadReportMail($user));
        }

        Log::info('未読報告書メール送信完了: ' . $users->count() . '件');
    }
}
