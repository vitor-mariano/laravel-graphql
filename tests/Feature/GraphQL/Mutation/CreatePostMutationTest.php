<?php

namespace Tests\Feature\GraphQL\Mutation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Post;
use JWTAuth;

class CreatePostMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "create post" mutation.
     *
     * @return void
     */
    public function testCreatePostMutation()
    {
        $post = factory(Post::class)->make();
        
        $token = JWTAuth::fromUser($post->user);
        
        $query = '
            mutation createPost(
                $token: String!,
                $body: String!
            ) {
                createPost(
                    token: $token,
                    body: $body
                ) {
                    body,
                    user {
                        name
                    }
                }
            }
        ';
        
        $params = [
            'token' => $token,
            'body' => $post->body
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response returns the expected object.
            ->assertJsonFragment([
                'createPost' => [
                    'body' => $post->body,
                    'user' => [
                        'name' => $post->user->name
                    ]
                ]
            ]);
    }
}
