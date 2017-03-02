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
        
        $anotherFriend = factory(User::class)->create();
        
        $me->following()->attach([$friend->id, $anotherFriend->id]);
        
        $post = $friend->posts()->save(factory(Post::class)->make());
        
        $anotherPost = $anotherFriend->posts()->save(factory(Post::class)->make());
        
        $token = JWTAuth::fromUser($me);
        
        $query = '
            query getFeed(
                $token: String!,
                $limit: Int,
                $offset: Int
            ) {
                feed(
                    token: $token,
                    limit: $limit,
                    offset: $offset
                ) {
                    id,
                    body,
                    user {
                        name
                    }
                }
            }
        ';
        
        $params = compact('token');
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response returns the expected object.
            ->assertJsonFragment([
                'feed' => [
                    [
                        'id' => $anotherPost->id,
                        'body' => $anotherPost->body,
                        'user' => [
                            'name' => $anotherFriend->name
                        ]
                    ],
                    [
                        'id' => $post->id,
                        'body' => $post->body,
                        'user' => [
                            'name' => $friend->name
                        ]
                    ]
                ]
            ]);
        
        // Test limit and offset.
        
        $params = [
            'token' => $token,
            'limit' => 1,
            'offset' => 1
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
