<?php

namespace App\Http\Middleware;

use App\Support\LogScrubber;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestLogger
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);
        $response = $next($request);
        $duration = (int) round((microtime(true) - $startedAt) * 1000);
        $statusText = Response::$statusTexts[$response->getStatusCode()] ?? 'Unknown';
        $userId = optional($request->user('api'))->id;
        $line = sprintf(
            '[%s] API %s /%s %d %s %dms',
            now()->toDateTimeString(),
            $request->method(),
            ltrim($request->path(), '/'),
            $response->getStatusCode(),
            $statusText,
            $duration
        );

        Log::info($line, [
            'method' => $request->method(),
            'path' => '/'.ltrim($request->path(), '/'),
            'query' => LogScrubber::redact($request->query()),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'ip' => $request->ip(),
            'user_id' => $userId,
            'request_id' => $request->headers->get('X-Request-Id'),
            'timestamp' => now()->toIso8601String(),
        ]);

        if (filter_var(config('logging.inventory_cli', false), FILTER_VALIDATE_BOOL)) {
            error_log($line);
        }

        return $response;
    }
}
