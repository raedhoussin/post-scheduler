<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;

class AnalyticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/analytics/posts",
     *     summary="Get posts analytics by platform",
     *     tags={"Analytics"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Post analytics data",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(
     *                 @OA\Property(property="platform_id", type="integer"),
     *                 @OA\Property(property="platform_name", type="string"),
     *                 @OA\Property(property="total_posts", type="integer"),
     *                 @OA\Property(property="published_posts", type="integer"),
     *                 @OA\Property(property="scheduled_posts", type="integer"),
     *                 @OA\Property(property="publish_success_rate", type="number", format="float", description="Percentage")
     *             )
     *         )
     *     )
     * )
     */
    public function postsAnalytics()
    {
        $analytics = Platform::query()
            ->withCount([
                'posts as total_posts',
                'posts as published_posts' => function($query) {
                    $query->where('post_platforms.status', 'published');
                },
                'posts as scheduled_posts' => function($query) {
                    $query->where('post_platforms.status', 'scheduled');
                }
            ])
            ->select(
                'platforms.id',
                'platforms.name',
                'platforms.type'
            )
            ->get()
            ->map(function ($platform) {
                // حساب النسبة المئوية مع تجنب القسمة على صفر
                $publishRate = $platform->total_posts > 0 
                    ? round(($platform->published_posts / $platform->total_posts) * 100, 2)
                    : 0;
    
                return [
                    'platform_id' => $platform->id,
                    'platform_name' => $platform->name,
                    'platform_type' => $platform->type,
                    'total_posts' => $platform->total_posts,
                    'published_posts' => $platform->published_posts,
                    'scheduled_posts' => $platform->scheduled_posts,
                    'publish_success_rate' => $publishRate
                ];
            });
    
        return response()->json($analytics);
    }
}