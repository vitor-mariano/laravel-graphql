<?php

namespace Tests\Feature\GraphQL\Query;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Post;

class UserQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "find user" query.
     *
     * @return void
     */
    public function testFindUserQuery()
    {
        $user = factory(User::class)->create();
        
        $post = $user->posts()->save(
            factory(Post::class)->make()
        );
        
        $query = '
            query findUser($id: Int!) {
                user(id: $id) {
                    id,
                    name,
                    email,
                    posts {
                        body
                    },
                    created_at,
                    updated_at
                }
            }
        ';
        
        $params = [
            'id' => $user->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response has the expected object.
            ->assertJsonFragment([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'posts' => [
                        [
                            'body' => $post->body
                        ]
                    ],
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
    }
    
    /**
     * Test if API returns an error when password is queried.
     *
     * @return void
     */
    public function testPasswordCannotBeQueried()
    {
        $user = factory(User::class)->create();
        
        $query = '
            query findUser($id: Int!) {
                user(id: $id) {
                    name,
                    password
                }
            }
        ';
        
        $params = [
            'id' => $user->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But cannot get password.
            ->assertJsonFragment(['message' => 'Cannot query field "password" on type "User".']);
    }
}
