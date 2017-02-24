<?php

namespace Tests\Feature\Graphql\Query;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use JWTAuth;

class ProfileQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test "profile" query.
     *
     * @return void
     */
    public function testProfileQuery()
    {
        $user = factory(User::class)->create();
        
        $query = '
            query getProfile($token: String!) {
                profile(token: $token) {
                    name
                }
            }
        ';
        
        $params = [
            'token' => JWTAuth::fromUser($user)
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'profile' => [
                    'name' => $user->name
                ]
            ]);
    }
}
