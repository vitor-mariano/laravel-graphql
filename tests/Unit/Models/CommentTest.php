<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test if migration and model are configured correctelly.
     *
     * @return void
     */
    public function testSanity()
    {
        $comment = factory(Comment::class)->create();
        
        $this->assertTrue($comment->exists);
        
        // Assert relationship with User model is well defined.
        $this->assertNotNull($comment->user);
        
        // Assert relationship with Post model is well defined.
        $this->assertNotNull($comment->post);
    }
}
