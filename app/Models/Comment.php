<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Post;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['body'];
    
    /**
     * User who owns the comment.
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Post who contains the comment.
     *
     * @return void
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
