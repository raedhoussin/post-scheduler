<?php

namespace App\Models;

/**
 * @OA\Schema(
 *     schema="ActivityLog",
 *     type="object",
 *     title="Activity Log Model",
 *     description="Represents system activity records",
 *     required={"id", "user_id", "action", "description"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1,
 *         description="Unique identifier for the activity log"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=5,
 *         description="ID of the user who performed the activity"
 *     ),
 *     @OA\Property(
 *         property="action",
 *         type="string",
 *         example="Created post",
 *         description="Type of activity performed",
 *         enum={"Created post", "Updated post", "Deleted post"}
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="User created a new post",
 *         description="Detailed description of the activity"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-05-26T10:00:00Z",
 *         description="Timestamp when activity was recorded"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-05-26T10:00:00Z",
 *         description="Timestamp when activity was last updated"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         allOf={
 *             @OA\Schema(ref="#/components/schemas/User"),
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="id",
 *                     type="integer",
 *                     example=5
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="John Doe"
 *                 )
 *             )
 *         },
 *         description="Associated user object (nullable)"
 *     )
 * )
 */
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'description'];


     /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}