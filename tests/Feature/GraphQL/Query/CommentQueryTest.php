<?php

namespace Tests\Feature\GraphQL\Query;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Comment;

class CommentQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "find comment" query.
     *
     * @return void
     */
    public function testFindCommentQuery()
    {
        $comment = factory(Comment::class)->create();
        
        $query = '
            query findComment($id: Int!) {
                comment(id: $id) {
                    id,
                    user {
                        name
                    },
                    post {
                        body
                    },
                    body,
                    created_at,
                    updated_at
                }
            }
        ';
        
        $params = [
            'id' => $comment->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response returns the expected object.
            ->assertJsonFragment([
                'comment' => [
                    'id' => $comment->id,
                    'user' => [
                        'name' => $comment->user->name
                    ],
                    'post' => [
                        'body' => $comment->post->body
                    ],
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
    }
}
