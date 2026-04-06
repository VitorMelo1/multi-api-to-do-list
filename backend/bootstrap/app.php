<?php

use App\Services\AuditLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e): void {
            if ($e instanceof ValidationException) {
                return;
            }
            if ($e instanceof HttpExceptionInterface) {
                return;
            }
            if (! app()->runningInConsole()) {
                try {
                    AuditLogger::record(
                        'erro',
                        $e::class.': '.$e->getMessage(),
                        auth()->id()
                    );
                } catch (Throwable) {
                }
            }
        });
    })->create();
