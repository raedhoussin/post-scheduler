<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PostPlatform Model
 * 
 * Represents the pivot relationship between Posts and Platforms,
 * tracking the publishing status on each platform.
 * 
 * @property int $post_id
 * @property int $platform_id
 * @property string $status Publishing status ('pending', 'published', 'failed')
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class PostPlatform extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'post_id',      // Related Post ID
        'platform_id',  // Related Platform ID
        'status',      // Publishing status
    ];

    /**
     * Relationship: Belongs to a Post
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    /**
     * Relationship: Belongs to a Platform
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}