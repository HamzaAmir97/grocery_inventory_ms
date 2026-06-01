<?php

namespace App\Http\OpenApi;

use OpenApi\Attributes as OA;

#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
class SecuritySchemes {}
