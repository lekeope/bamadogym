<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureStaff;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'staff' => EnsureStaff::class,
            'admin' => EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            $isDbDown = function (Throwable $error) use (&$isDbDown): bool {
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

                if ($error instanceof PDOException) {
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
                if ($previous instanceof Throwable) {
                    return $isDbDown($previous);
                }

                return $messageLooksLikeDisconnect($error->getMessage());
            };

            if (! $isDbDown($e)) {
                return null;
            }

            $raw = $e->getMessage();
            $root = $e;
            while ($root->getPrevious() instanceof Throwable) {
                $root = $root->getPrevious();
            }
            $technical = $root->getMessage() ?: $raw;

            // Unwrap nested "attempting to log:" / permission spam from storage/logs.
            if (preg_match('/SQLSTATE\[[^\]]+\].+$/s', $technical, $matches)) {
                $technical = trim($matches[0]);
            } elseif (preg_match('/(?:connection to server|password authentication failed|could not connect)[^.]*(?:\.[^.]*)?/i', $technical, $matches)) {
                $technical = trim($matches[0]);
            }

            $technical = Str::limit(preg_replace('/\s+/', ' ', $technical) ?? $technical, 280);

            $lower = strtolower($technical.$raw);
            $cause = match (true) {
                str_contains($lower, 'password authentication failed') => 'Wrong database username or password',
                str_contains($lower, 'connection refused') => 'Postgres is not reachable on that host/port',
                str_contains($lower, 'no such host'), str_contains($lower, 'name or service not known') => 'Database host name is wrong',
                str_contains($lower, 'does not exist') => 'Database name does not exist',
                str_contains($lower, 'timed out') => 'Timed out connecting to Postgres',
                default => 'Could not connect to PostgreSQL',
            };

            $connection = [
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
                'username' => config('database.connections.pgsql.username'),
            ];

            $dbUrl = config('database.connections.pgsql.url');
            if ($dbUrl && blank($connection['host'])) {
                $parts = parse_url($dbUrl);
                $connection = [
                    'host' => $parts['host'] ?? null,
                    'port' => isset($parts['port']) ? (string) $parts['port'] : ($connection['port'] ?? '5432'),
                    'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : null,
                    'username' => $parts['user'] ?? null,
                ];
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Database unavailable',
                    'cause' => $cause,
                    'error' => $technical,
                    'connection' => $connection,
                ], 503);
            }

            return response()->view('errors.database', [
                'cause' => $cause,
                'technical' => $technical,
                'connection' => $connection,
            ], 503);
        });
    })->create();
