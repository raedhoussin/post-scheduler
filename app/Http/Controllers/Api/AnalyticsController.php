<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/analytics/posts",
     *     operationId="getPostsAnalytics",
     *     summary="Get posts analytics aggregated by platform",
     *     description="Retrieves comprehensive statistics about posts distribution and publishing success rate across all platforms",
     *     tags={"Analytics"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="platform_id",
     *                     type="integer",
     *                     example=1,
     *                     description="Unique identifier of the platform"
     *                 ),
     *                 @OA\Property(
     *                     property="platform_name",
     *                     type="string",
     *                     example="Twitter",
     *                     description="Display name of the platform"
     *                 ),
     *                 @OA\Property(
     *                     property="platform_type",
     *                     type="string",
     *                     example="social",
     *                     description="Category of the platform"
     *                 ),
     *                 @OA\Property(
     *                     property="total_posts",
     *                     type="integer",
     *                     example=25,
     *                     description="Total number of posts scheduled for this platform"
     *                 ),
     *                 @OA\Property(
     *                     property="published_posts",
     *                     type="integer",
     *                     example=20,
     *                     description="Number of successfully published posts"
     *                 ),
     *                 @OA\Property(
     *                     property="scheduled_posts",
     *                     type="integer",
     *                     example=5,
     *                     description="Number of posts pending publication"
     *                 ),
     *                 @OA\Property(
     *                     property="publish_success_rate",
     *                     type="number",
     *                     format="float",
     *                     example=80.0,
     *                     description="Percentage of successfully published posts (0-100)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error occurred")
     *         )
     *     )
     * )
     *
     * Retrieves post analytics data aggregated by platform.
     * 
     * Calculates:
     * - Total posts per platform
     * - Published vs scheduled counts
     * - Success rate percentage
     * 
     * Features:
     * - Uses caching to improve performance (1 minute TTL)
     * - Handles division by zero cases when calculating success rate
     * - Returns data in a structured format for frontend consumption
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function postsAnalytics()
     {
         $cacheKey = 'posts_analytics_global';
         $ttl = 300; // 300 / 60 seconds  (5 minute)
     
         $analytics = Cache::remember($cacheKey, $ttl, function () {
             // Fetch platform data
             $platforms = DB::table('platforms')->select('id', 'name', 'type')->get();
     
             // Get total post count per platform
             $totalPosts = DB::table('post_platforms')
                 ->select('platform_id', DB::raw('COUNT(post_id) as total_posts'))
                 ->groupBy('platform_id')
                 ->pluck('total_posts', 'platform_id');
     
             // Get published post count per platform
             $publishedPosts = DB::table('post_platforms')
                 ->where('status', 'published')
                 ->select('platform_id', DB::raw('COUNT(post_id) as published_posts'))
                 ->groupBy('platform_id')
                 ->pluck('published_posts', 'platform_id');
     
             // Get scheduled post count per platform
             $scheduledPosts = DB::table('post_platforms')
                 ->where('status', 'scheduled')
                 ->select('platform_id', DB::raw('COUNT(post_id) as scheduled_posts'))
                 ->groupBy('platform_id')
                 ->pluck('scheduled_posts', 'platform_id');
     
             // Build final result by combining data and calculating success rate
             return $platforms->map(function ($platform) use ($totalPosts, $publishedPosts, $scheduledPosts) {
                 $total = $totalPosts->get($platform->id, 0);
                 $published = $publishedPosts->get($platform->id, 0);
                 $scheduled = $scheduledPosts->get($platform->id, 0);
                 $publishRate = $total > 0 ? round(($published / $total) * 100, 2) : 0;
     
                 return [
                     'platform_id' => $platform->id,
                     'platform_name' => $platform->name,
                     'platform_type' => $platform->type,
                     'total_posts' => $total,
                     'published_posts' => $published,
                     'scheduled_posts' => $scheduled,
                     'publish_success_rate' => $publishRate,
                 ];
             });
         });
     
         return response()->json($analytics);
     }
}