<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['body'];
    
    /**
     * User who owns the post.
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Post comments.
     *
     * @return void
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
