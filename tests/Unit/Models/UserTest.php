<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test if migration and model are configured correctelly.
     *
     * @return void
     */
    public function testSanity()
    {
        $user = factory(User::class)->create();
        
        $this->assertTrue($user->exists);
        
        // Test user following.
        
        $followable = factory(User::class)->create();
        
        $user->following()->attach($followable->id);
        
        // Assert user is following another user.
        $this->assertTrue($user->follows($followable->id));
        
        // Assert user is not followed by another user.
        $this->assertFalse($user->followerdBy($followable->id));
        
        // Test user posts.
        
        $post = $followable->posts()->save(
            factory(Post::class)->make()
        );
        
        $this->assertTrue($post->exists);
        
        // Test user comments.
        
        $comment = $user->comments()->save(
            factory(Comment::class)->make()
        );
        
        $this->assertTrue($comment->exists);
        
        // Test user feed.
        
        $posts = $user->feed()->get();
        
        $this->assertEquals($posts->toArray(), [$post->toArray()]);
    }
}
