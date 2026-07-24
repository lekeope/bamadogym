<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'staff' => \App\Http\Middleware\EnsureStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            $isDbDown = function (\Throwable $error) use (&$isDbDown): bool {
                $messageLooksLikeDisconnect = function (string $message): bool {
                    $message = strtolower($message);

                    return str_contains($message, 'connection refused')
                        || str_contains($message, 'could not connect')
                        || str_contains($message, 'sqlstate[08006]')
                        || str_contains($message, 'sqlstate[08001]')
                        || str_contains($message, 'no such host')
                        || str_contains($message, 'name or service not known')
                        || str_contains($message, 'connection timed out')
                        || str_contains($message, 'server closed the connection')
                        || str_contains($message, 'is not currently accepting connections')
                        || str_contains($message, 'password authentication failed')
                        || (str_contains($message, 'database "') && str_contains($message, 'does not exist'));
                };

                if ($error instanceof \PDOException) {
                    $sqlState = (string) ($error->errorInfo[0] ?? $error->getCode());

                    // Class 08xxx = connection exception.
                    if (str_starts_with($sqlState, '08')) {
                        return true;
                    }

                    return $messageLooksLikeDisconnect($error->getMessage());
                }

                if ($error instanceof QueryException) {
                    $sqlState = (string) ($error->errorInfo[0] ?? '');
                    if (str_starts_with($sqlState, '08') || $messageLooksLikeDisconnect($error->getMessage())) {
                        return true;
                    }
                }

                $previous = $error->getPrevious();
                if ($previous instanceof \Throwable) {
                    return $isDbDown($previous);
                }

                return $messageLooksLikeDisconnect($error->getMessage());
            };

            if (! $isDbDown($e)) {
                return null;
            }

            $message = $e->getMessage();
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Database unavailable',
                    'error' => $message,
                ], 503);
            }

            return response()->view('errors.database', [
                'message' => $message,
            ], 503);
        });
    })->create();
