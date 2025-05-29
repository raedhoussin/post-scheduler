<?php

namespace App\Models;
/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     required={"title", "content", "status", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="My first post"),
 *     @OA\Property(property="content", type="string", example="This is the post content"),
 *     @OA\Property(property="image_url", type="string", format="url", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example="2025-05-23T15:00:00Z"),
 *     @OA\Property(property="status", type="string", enum={"draft", "scheduled", "published"}, example="scheduled"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-22T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-22T12:30:00Z"),
 * )
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'scheduled_at',
        'status',
        'user_id',
    ];
    

    public function user()
{
    return $this->belongsTo(User::class);
}

public function platforms()
{
    return $this->belongsToMany(Platform::class, 'post_platforms')
                ->withPivot('status')
                ->withTimestamps();
}

}