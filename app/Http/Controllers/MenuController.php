<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class MenuController extends Controller
{
    public function menu()
    {
        $userId = User::findOrFail(Auth::id())->id;

        $reports=DB::table('reports')
        ->leftJoin('report_reads', function ($join) use ($userId) {
            $join->on('reports.id', '=', 'report_reads.report_id')
                 ->where('report_reads.user_id', '=', $userId);
        })
        ->wherenull('report_reads.user_id')
        ->exists();
        // ->get();

        $comments=DB::table('comments')
        ->leftJoin('comment_reads', function ($join) use ($userId) {
            $join->on('comments.id', '=', 'comment_reads.comment_id')
                 ->where('comment_reads.user_id', '=', $userId);
        })
        ->wherenull('comment_reads.user_id')
        ->exists();
        // ->get();

        // dd($reports,$comments);

        return Inertia::render('Menu', [
            'reports' => $reports,
            'comments' => $comments,
        ]);
    }
}
