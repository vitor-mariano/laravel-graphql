<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Post;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * User posts.
     *
     * @return Illuminate\Eloquent\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    /**
     * User comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Following users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follower_user', 'follower_id', 'user_id');
    }
    
    /**
     * Check if user follows another user.
     *
     * @param  int $userId
     * @return bool
     */
    public function follows($userId)
    {
        return $this->following()->where('user_id', $userId)->exists();
    }
    
    /**
     * Followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follower_user', 'user_id', 'follower_id');
    }
    
    /**
     * Check if user is followerd by another user.
     *
     * @param  int $followerId
     * @return bool
     */
    public function followerdBy($followerId)
    {
        return $this->followers()->where('follower_id', $followerId)->exists();
    }
    
    /**
     * User feed.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function feed()
    {
        return Post::whereIn('user_id', $this->following()->pluck('id'));
    }
}
