<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Sign in and receive a session credential',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sign-in succeeded',
                content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
            new OA\Response(
                response: 405,
                description: 'Method not allowed.',
                content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected backend failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
            ),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $token = JWTAuth::attempt($credentials);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => UserResource::make(User::query()->where('email', $credentials['email'])->sole())->resolve($request),
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Discard the current session credential',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Signed out',
                content: new OA\JsonContent(
                    required: ['success', 'message'],
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Successfully signed out.'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Missing, invalid, expired, or signed-out token.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 405,
                description: 'Method not allowed.',
                content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected backend failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
            ),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully signed out.',
        ]);
    }

    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Return the authenticated user profile',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile returned',
                content: new OA\JsonContent(
                    required: ['success', 'message', 'data'],
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'User retrieved.'),
                        new OA\Property(
                            property: 'data',
                            required: ['id', 'name', 'email'],
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Missing, invalid, expired, or signed-out token.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 405,
                description: 'Method not allowed.',
                content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected backend failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
            ),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User retrieved.',
            'data' => UserResource::make($request->user('api'))->resolve($request),
        ]);
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        summary: 'Exchange the current token for a fresh one',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token refreshed.',
                content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Missing, invalid, expired, or signed-out token.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 405,
                description: 'Method not allowed.',
                content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected backend failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
            ),
        ]
    )]
    public function refresh(Request $request): JsonResponse
    {
        $token = JWTAuth::parseToken()->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => UserResource::make($request->user('api'))->resolve($request),
            ],
        ]);
    }
}
