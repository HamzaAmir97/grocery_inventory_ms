<?php

use App\Exceptions\ConflictException;
use App\Exceptions\DeleteRestrictedException;
use App\Exceptions\SubcategoryMismatchException;
use App\Http\Middleware\ApiRequestLogger;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\SecurityHeaders;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request): ?string => $request->is('api/*') ? null : '/login');
        $middleware->api(append: [
            RequestId::class,
            SecurityHeaders::class,
            'throttle:api',
            ApiRequestLogger::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $notFoundMessage = function (Request $request): string {
            $segments = collect($request->segments());
            $segment = $segments->last();

            if (is_numeric($segment)) {
                $segment = $segments->slice(-2, 1)->first();
            }

            return Str::of($segment ?: 'record')
                ->singular()
                ->replace('-', ' ')
                ->headline()
                ->append(' not found.')
                ->toString();
        };

        $exceptions->render(function (DeleteRestrictedException $exception) {
            return ApiResponse::conflict($exception->getMessage());
        });

        $exceptions->render(function (ConflictException $exception) {
            return ApiResponse::conflict($exception->getMessage());
        });

        $exceptions->render(function (SubcategoryMismatchException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::validation(['subcategory_id' => [$exception->getMessage()]]);
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthenticated();
            }
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::forbidden();
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::forbidden();
            }
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::validation($exception->errors());
            }
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) use ($notFoundMessage) {
            if ($request->is('api/*')) {
                return ApiResponse::notFound($notFoundMessage($request));
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($notFoundMessage) {
            if ($request->is('api/*')) {
                return ApiResponse::notFound($notFoundMessage($request));
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::methodNotAllowed();
            }
        });

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                ], 429);
            }
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $sqlstate = (string) $exception->getCode();
            $class = substr($sqlstate, 0, 2);

            if ($class === '23') {
                $message = match ($sqlstate) {
                    '23503' => 'This action would break a relationship between records.',
                    '23502' => 'A required field is missing.',
                    '23514' => 'A submitted value violates a data integrity rule.',
                    default => 'The submitted data conflicts with an existing record.',
                };

                return ApiResponse::validation(['database' => [$message]]);
            }

            return null;
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            Log::error('Unhandled API exception', [
                'request_id' => $request->header('X-Request-Id'),
                'route' => $request->path(),
                'method' => $request->method(),
                'user_id' => optional($request->user('api'))->id,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace_first' => Str::limit($exception->getTraceAsString(), 1000),
            ]);

            return ApiResponse::serverError(
                app()->environment('local') ? $exception->getMessage() : 'Server error.'
            );
        });
    })->create();
