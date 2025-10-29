<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendUnreadReportNotificationJob;
use App\Jobs\SendUnreadcOMMENTNotificationJob;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

    })
    ->withSchedule(function ($schedule) {
        // クロージャの引数に型宣言は不要

        $schedule->job(new SendUnreadReportNotificationJob())->weekdays()->at('09:50');
        $schedule->job(new SendUnreadCommentNotificationJob())->weekdays()->at('10:05');

        $schedule->job(new SendUnreadReportNotificationJob())->weekdays()->at('13:50');
        $schedule->job(new SendUnreadCommentNotificationJob())->weekdays()->at('14:05');

        $schedule->job(new SendUnreadReportNotificationJob())->weekdays()->at('17:50');
        $schedule->job(new SendUnreadCommentNotificationJob())->weekdays()->at('18:05');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
