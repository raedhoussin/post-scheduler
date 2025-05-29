<?php

namespace App\Models;

/**
 * @OA\Schema(
 *     schema="User",
 *     description="User model representing a registered application user",
 *     @OA\Property(property="id", type="integer", example=1, description="Unique user identifier"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address")
 * )
 */
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes for user creation/updating
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',      // User's full name
        'email',     // Unique email address
        'password', // Hashed password
    ];

    /**
     * Attributes excluded from serialization (API responses)
     * 
     * @var array<int, string>
     */
    protected $hidden = [
        'password',        // Never expose hashed password
        'remember_token', // Session token
    ];

    /**
     * Attribute type casting
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Automatic date conversion
        'password' => 'hashed',          // Automatic password hashing
    ];

    /**
     * Relationship: User to Platforms (Many-to-Many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 
     * Features:
     * - Pivot table includes 'enabled' flag to track user-platform access
     * - Automatically maintains timestamps for relationship changes
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class)
               ->withPivot('enabled')  // Additional pivot field
               ->withTimestamps();    // Track relationship timestamps
    }
    
    /**
     * Relationship: User to Posts (One-to-Many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 
     * Description:
     * - A user can create multiple scheduled posts
     * - Posts are automatically linked to the creator
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}