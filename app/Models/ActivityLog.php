<?php

namespace App\Models;

/**
 * @OA\Schema(
 *     schema="ActivityLog",
 *     type="object",
 *     title="Activity Log Model",
 *     description="Tracks and records all significant user actions within the system for auditing and monitoring purposes",
 *     required={"id", "user_id", "action", "description"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1,
 *         description="Auto-incrementing primary key identifier"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=5,
 *         description="Foreign key reference to the user who performed the action"
 *     ),
 *     @OA\Property(
 *         property="action",
 *         type="string",
 *         example="Created post",
 *         description="Categorized action type",
 *         enum={
 *             "Created post", 
 *             "Updated post", 
 *             "Deleted post",
 *             "Scheduled post",
 *             "Published post",
 *             "Platform connected",
 *             "Platform disconnected"
 *         }
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="User created a new post titled 'Summer Campaign'",
 *         description="Detailed contextual information about the activity",
 *         maxLength=500
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-05-26T10:00:00Z",
 *         description="Automatic timestamp of when the activity was logged"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-05-26T10:00:00Z",
 *         description="Timestamp of last update (typically matches created_at)"
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
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     example="john@example.com"
 *                 )
 *             )
 *         },
 *         description="Associated user data through Eloquent relationship"
 *     )
 * )
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'user_id',     // ID of the user who performed the action
        'action',     // Type of action performed (from enum)
        'description' // Detailed description of the activity
    ];

    /**
     * Relationship: The user who performed this activity.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * 
     * Note: Uses soft deletion if the User model implements it
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}