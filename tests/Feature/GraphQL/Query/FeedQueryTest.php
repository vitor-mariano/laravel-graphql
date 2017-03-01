<?php

namespace Tests\Feature\GraphQL\Query;

use JWTAuth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Post;
use App\Models\User;

class FeedQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "get feed" query.
     *
     * @return void
     */
    public function testGetFeedQuery()
    {
        $me = factory(User::class)->create();
        
        $friend = factory(User::class)->create();
        
        $me->following()->attach($friend->id);
        
        $post = $friend->posts()->save(factory(Post::class)->make());
        
        $query = '
            query getFeed($token: String!) {
                feed(token: $token) {
                    id,
                    body,
                    user {
                        name
                    }
                }
            }
        ';
        
        $params = [
            'token' => JWTAuth::fromUser($me)
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response returns the expected object.
            ->assertJsonFragment([
                'feed' => [
                    [
                        'id' => $post->id,
                        'body' => $post->body,
                        'user' => [
                            'name' => $friend->name
                        ]
                    ]
                ]
            ]);
    }
}
