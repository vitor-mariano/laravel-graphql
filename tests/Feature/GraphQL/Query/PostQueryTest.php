<?php

namespace Tests\Feature\Graphql\Query;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Post;
use App\Models\Comment;

class PostQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "find post" query.
     *
     * @return void
     */
    public function testFindPostQuery()
    {
        $post = factory(Post::class)->create();
        
        $comment = $post->comments()->save(
            factory(Comment::class)->make()
        );
        
        $query = '
            query findPost(
                $id: Int!
            ) {
                post(id: $id) {
                    id,
                    user {
                        name
                    },
                    body,
                    comments {
                        body
                    },
                    created_at,
                    updated_at
                }
            }
        ';
        
        $params = [
            'id' => $post->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response has the expected object.
            ->assertJsonFragment([
                'post' => [
                    'id' => $post->id,
                    'user' => [
                        'name' => $post->user->name
                    ],
                    'body' => $post->body,
                    'comments' => [
                        [
                            'body' => $comment->body
                        ]
                    ],
                    'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $post->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
    }
}
