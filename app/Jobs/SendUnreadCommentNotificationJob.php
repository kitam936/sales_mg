<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UnreadCommentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUnreadCommentNotificationJob
{
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // mailService=true で、未読報告書があるユーザーを取得
        $users = User::where('mailService', true)
            ->where(function($q) {
                $q->where('dept_id', '<=', 3);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('comments')
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('comment_reads')
                            ->whereColumn('comment_reads.comment_id', 'comments.id')
                            ->whereColumn('comment_reads.user_id', 'users.id');
                    });
            })
            ->get();

            // dd($users);

        if ($users->isEmpty()) {
            Log::info('未読コメントの該当者なし: ' . now());
            return;
        }

        foreach ($users as $user) {
            Mail::to($user->email)->send(new UnreadCommentMail($user));
        }

        Log::info('未読コメントメール送信完了: ' . $users->count() . '件');
    }
}
