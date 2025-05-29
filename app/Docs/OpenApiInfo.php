<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Post Scheduler API",
 *     description="API documentation for Laravel Post Scheduler project",
 *     @OA\Contact(
 *         email="raedhoussin33@gmail.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter token to authorize"
 * )
 */

class OpenApiInfo
{
}