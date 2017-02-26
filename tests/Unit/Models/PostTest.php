<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Post;

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
    }
    
    /**
     * Test relationship with User model.
     * @return void
     */
    public function testRelationshipWithUserModel()
    {
        $post = factory(Post::class)->create();
        
        $this->assertNotNull($post->user);
    }
}
