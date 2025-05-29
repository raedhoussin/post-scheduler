<?php

namespace App\Models;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Represents a social media post that can be scheduled and published to multiple platforms",
 *     required={"title", "content", "status", "user_id"},
 *     @OA\Property(
 *         property="id", 
 *         type="integer", 
 *         example=1,
 *         description="Auto-incrementing unique identifier for the post"
 *     ),
 *     @OA\Property(
 *         property="title", 
 *         type="string", 
 *         example="My first post",
 *         description="The title/headline of the post (max 255 chars)"
 *     ),
 *     @OA\Property(
 *         property="content", 
 *         type="string", 
 *         example="This is the post content",
 *         description="The main body content of the post"
 *     ),
 *     @OA\Property(
 *         property="image_url", 
 *         type="string", 
 *         format="url", 
 *         nullable=true, 
 *         example="https://example.com/image.jpg",
 *         description="Optional URL to an associated image for the post"
 *     ),
 *     @OA\Property(
 *         property="scheduled_at", 
 *         type="string", 
 *         format="date-time", 
 *         nullable=true, 
 *         example="2025-05-23T15:00:00Z",
 *         description="The date/time when the post should be published. Null for immediate posting."
 *     ),
 *     @OA\Property(
 *         property="status", 
 *         type="string", 
 *         enum={"draft", "scheduled", "published"}, 
 *         example="scheduled",
 *         description="Current publication status of the post"
 *     ),
 *     @OA\Property(
 *         property="user_id", 
 *         type="integer", 
 *         example=1,
 *         description="ID of the user who created the post"
 *     ),
 *     @OA\Property(
 *         property="created_at", 
 *         type="string", 
 *         format="date-time", 
 *         example="2025-05-22T12:00:00Z",
 *         description="Timestamp when the post was first created"
 *     ),
 *     @OA\Property(
 *         property="updated_at", 
 *         type="string", 
 *         format="date-time", 
 *         example="2025-05-22T12:30:00Z",
 *         description="Timestamp when the post was last updated"
 *     ),
 * )
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'title',         // Post title/headline
        'content',      // Main post content
        'image_url',    // Optional associated image
        'scheduled_at', // Planned publication datetime
        'status',      // Current post status
        'user_id',     // Creator of the post
    ];

    /**
     * Relationship: The user who created this post.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The platforms this post will be published to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 
     * Features:
     * - Uses the 'post_platforms' pivot table
     * - Tracks individual platform status via pivot 'status' field
     * - Maintains timestamps for relationship changes
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'post_platforms')
                    ->withPivot('status')    // Tracks publishing status per platform
                    ->withTimestamps();     // Records when platform was added/updated
    }
}