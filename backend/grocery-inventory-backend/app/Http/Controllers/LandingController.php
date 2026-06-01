<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use App\Support\ServiceIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function show(Request $request): JsonResponse|View
    {
        $pointer = [
            'service' => ServiceIdentity::toArray(),
            'documentation_url' => url('/api/documentation'),
            'status_url' => route('status.show'),
            'authentication' => [
                'login_path' => '/api/auth/login',
                'guidance' => 'Sign in via POST /api/auth/login with the demo admin credentials shown in the documentation, then send the returned Bearer token.',
            ],
        ];

        if ($request->expectsJson()) {
            return ApiResponse::ok($pointer, 'Grocery Inventory Management API.');
        }

        return view('landing', $pointer);
    }
}
