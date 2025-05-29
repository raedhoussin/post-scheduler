<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPlatform extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'platform_id',
        'status',
    ];
    
    
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
    

}