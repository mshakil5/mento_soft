<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([

        ]);
        $middleware->alias([
            'is_admin' => App\Http\Middleware\IsAdmin::class,
            'is_manager' => App\Http\Middleware\IsManager::class,
            'is_user' => App\Http\Middleware\IsUser::class,

            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // $schedule->command('project-service:auto-create')->everyFiveMinutes();
        $schedule->command('project-service:auto-create')->everyMinute();
        //php artisan schedule:work
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();