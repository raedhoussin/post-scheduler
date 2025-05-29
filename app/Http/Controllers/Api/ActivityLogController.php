<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="ActivityLogs",
 *     description="API Endpoints for Activity Logs"
 * )
 */
class ActivityLogController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/activity-logs",
     *     summary="Get list of latest 50 activity logs with user details",
     *     tags={"ActivityLogs"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of activity logs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ActivityLog")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $logs = ActivityLog::with('user')->latest()->paginate($perPage);
        return response()->json($logs);
    }
    
}