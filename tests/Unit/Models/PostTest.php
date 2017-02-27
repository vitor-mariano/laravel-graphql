<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Post;
use App\Models\Comment;

class PostTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test if migration and model are configured correctelly.
     *
     * @return void
     */
    public function testSanity()
    {
        $post = factory(Post::class)->create();
        
        $this->assertTrue($post->exists);
        
        // Assert relationship with User model is well defined.
        $this->assertNotNull($post->user);
        
        // Assert relationship with Comment model is well defined.
        
        $comment = $post->comments()->save(
            factory(Comment::class)->make()
        );
        
        $this->assertTrue($comment->exists);
    }
}
