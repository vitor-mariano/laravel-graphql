<?php

namespace Tests\Feature\GraphQL\Mutation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use JWTAuth;

class FollowUserMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    const FOLLOW_USER_QUERY = '
        mutation followUser(
            $token: String!,
            $user_id: Int!
        ) {
            followUser(
                token: $token,
                user_id: $user_id
            ) {
                id,
                name
            }
        }
    ';
    
    /**
     * Test "follow user" mutation.
     *
     * @return void
     */
    public function testFollowUserMutation()
    {
        $me = factory(User::class)->create();
        $you = factory(User::class)->create();
        
        $query = self::FOLLOW_USER_QUERY;
        
        $params = [
            'token' => JWTAuth::fromUser($me),
            'user_id' => $you->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response returns the expected object.
            ->assertJsonFragment([
                'followUser' => [
                    'id' => $you->id,
                    'name' => $you->name
                ]
            ]);
    }
    
    /**
     * Test wrong user ID.
     *
     * @return void
     */
    public function testWrongUserId()
    {
        $me = factory(User::class)->create();
        
        $query = self::FOLLOW_USER_QUERY;
        
        $params = [
            'token' => JWTAuth::fromUser($me),
            'user_id' => 0
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But the user id is invalid.
            ->assertJsonFragment([
                'validation' => [
                    'user_id' => ['The selected user id is invalid.']
                ]
            ]);
    }
    
    /**
     * Test follow self ID.
     *
     * @return void
     */
    public function testFollowSelfId()
    {
        $user = factory(User::class)->create();
        
        $query = self::FOLLOW_USER_QUERY;
        
        $params = [
            'token' => JWTAuth::fromUser($user),
            'user_id' => $user->id
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But the user id is invalid.
            ->assertJsonFragment([
                'message' => 'Invalid argument "user_id": cannot follow yourself.'
            ]);
    }
}
