<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\User;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($report)
    {
        // dd($id);
        $report_id = Report::findOrFail($report)->id;

        // dd($report_id);

        return Inertia::render('Comments/Create', [
            'report_id' => $report_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        // dd($request);
        $comment=Comment::create([
            'user_id' => Auth::User()->id,
            'report_id' => $request->report_id,
            'comment' => $request->comment,
        ]);

        // 追記
        DB::table('comment_reads')->upsert(
            [
                'comment_id' => $comment->id,
                'user_id'   => User::findOrFail(Auth::id())->id,
                'created_at'   => now(),
                'updated_at' => now()

            ],
            ['comment_id', 'user_id'], // uniqueキー
        // ['created_at','updated_at']               // 更新するカラム
        );

        return to_route('reports.show', ['report' => $request->report_id])->with([
            'message' => 'コメントが登録されました',
            'status' => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        $comment_show = DB::table('comments')
        ->join('users', 'users.id', '=', 'comments.user_id')
        ->select(['comments.id', 'comments.report_id','comments.user_id', 'users.name', 'comments.comment', 'comments.created_at'])
        ->where('comments.id', $comment->id)
        ->orderBy('comments.created_at', 'desc')
        ->first();

        $login_user = Auth::user()->id;
        // dd($comment_show, $login_user);

        // 追記
        DB::table('comment_reads')->upsert(
            [
                'comment_id' => $comment->id,
                'user_id'   => User::findOrFail(Auth::id())->id,
                'created_at'   => now(),
                'updated_at' => now()

            ],
            ['comment_id', 'user_id'], // uniqueキー
            ['updated_at']               // 更新するカラム
        );

        return Inertia::render('Comments/Show', [
            'comment' => $comment_show,
            'login_user' => $login_user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        $comment_show = DB::table('comments')
        ->join('users', 'users.id', '=', 'comments.user_id')
        ->select(['comments.id', 'comments.report_id','comments.user_id','users.name', 'comments.comment', 'comments.created_at'])
        ->where('comments.id', $comment->id)
        ->orderBy('comments.created_at', 'desc')
        ->first();

        // dd($comment_show);

        return Inertia::render('Comments/Edit', [
            'comment' => $comment_show,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $target_comment = Comment::findOrFail($comment->id);
        $target_comment->comment = $request->comment;
        $target_comment->save();

        // dd($target_comment);

        return to_route('comments.show', ['comment' => $comment->id])->with([
            'message' => 'コメントが更新されました',
            'status' => 'info',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $report_id = $comment->report_id;
        $target_comment = Comment::findOrFail($comment->id);
        $target_comment->delete();

        return to_route('reports.show', ['report' => $report_id])->with([
            'message' => 'コメントが削除されました',
            'status' => 'alert',
        ]);
    }
}
