<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatusService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class StatusController extends Controller
{
    #[OA\Get(
        path: '/api/status',
        summary: 'Service status and dependency reachability',
        description: 'Returns public service identity and lightweight dependency reachability without requiring a bearer token.',
        tags: ['Status'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service status retrieved.',
                content: new OA\JsonContent(ref: '#/components/schemas/StatusResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected backend failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
            ),
        ]
    )]
    public function show(StatusService $status): JsonResponse
    {
        return ApiResponse::ok($status->report(), 'Service status retrieved.');
    }
}
