<?php

namespace App\Models;

/**
 * @OA\Schema(
 *     schema="Platform",
 *     type="object",
 *     title="Platform",
 *     description="Represents a social media platform where posts can be published",
 *     required={"id", "name", "type"},
 *     @OA\Property(
 *         property="id", 
 *         type="integer", 
 *         example=1,
 *         description="Auto-incrementing unique identifier for the platform"
 *     ),
 *     @OA\Property(
 *         property="name", 
 *         type="string", 
 *         example="Twitter",
 *         description="The display name of the platform (e.g., Twitter, LinkedIn)"
 *     ),
 *     @OA\Property(
 *         property="type", 
 *         type="string", 
 *         example="social",
 *         description="Category of the platform (e.g., social, professional, media)"
 *     ),
 *     @OA\Property(
 *         property="created_at", 
 *         type="string", 
 *         format="date-time", 
 *         example="2025-05-23T14:00:00Z",
 *         description="Timestamp when the platform was added to the system"
 *     ),
 *     @OA\Property(
 *         property="updated_at", 
 *         type="string", 
 *         format="date-time", 
 *         example="2025-05-23T14:00:00Z",
 *         description="Timestamp when the platform record was last updated"
 *     )
 * )
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable when creating/updating a platform.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'name',  // Platform's display name (e.g., "Twitter")
        'type', // Platform category (e.g., "social", "professional")
    ];

    /**
     * Relationship: Posts scheduled for this platform.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 
     * Features:
     * - Uses 'post_platforms' as the pivot table
     * - Tracks publication status via pivot 'status' field
     * - Maintains timestamps for relationship changes
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_platforms')
                    ->withPivot('status')     // Tracks publishing status per post
                    ->withTimestamps();      //
    }

    /**
     * Relationship: Users who have access to this platform.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 
     * Features:
     * - Uses 'platform_user' as the pivot table
     * - Controls platform access via pivot 'enabled' flag
     * - Maintains timestamps for relationship changes
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'platform_user')
                    ->withPivot('enabled')   // Controls if user can publish to this platform
                    ->withTimestamps();     // Records when access was granted/updated
    }
}