<?php


namespace App\Models;

/**
 * @OA\Schema(
 *     schema="Platform",
 *     type="object",
 *     title="Platform",
 *     required={"id", "name", "type"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Twitter"),
 *     @OA\Property(property="type", type="string", example="social"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-23T14:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-23T14:00:00Z")
 * )
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];
    
    public function posts()
{
    return $this->belongsToMany(Post::class, 'post_platforms')
                ->withPivot('status')
                ->withTimestamps();
}

public function users()
{
    return $this->belongsToMany(User::class, 'platform_user')
                ->withPivot('enabled')
                ->withTimestamps();
}


}