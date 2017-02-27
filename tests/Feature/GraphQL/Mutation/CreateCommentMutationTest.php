<?php

namespace Tests\Feature\GraphQL\Mutation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Comment;
use JWTAuth;

class CreateCommentMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    const CREATE_COMMENT_QUERY = '
        mutation createComment(
            $token: String!,
            $post_id: Int!,
            $body: String!
        ) {
            createComment(
                token: $token,
                post_id: $post_id,
                body: $body
            ) {
                user {
                    id
                },
                post {
                    id
                },
                body
            }
        }
    ';
    
    /**
     * Test "create comment" mutation.
     *
     * @return void
     */
    public function testCreateMutationTest()
    {
        $comment = factory(Comment::class)->make();
        
        $query = self::CREATE_COMMENT_QUERY;
        
        $params = [
            'token' => JWTAuth::fromUser($comment->user),
            'post_id' => $comment->post->id,
            'body' => $comment->body
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'createComment' => [
                    'user' => [
                        'id' => $comment->user->id
                    ],
                    'post' => [
                        'id' => $comment->post->id
                    ],
                    'body' => $comment->body
                ]
            ]);
    }
    
    /**
     * Test when "create comment" mutation receives a wrong post ID.
     *
     * @return void
     */
    public function testWrongPostId()
    {
        $comment = factory(Comment::class)->make();
        
        $query = self::CREATE_COMMENT_QUERY;
        
        $params = [
            'token' => JWTAuth::fromUser($comment->user),
            'post_id' => 0,
            'body' => $comment->body
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But the post id is invalid.
            ->assertJsonFragment([
                'validation' => [
                    'post_id' => ['The selected post id is invalid.']
                ]
            ]);
    }
}
