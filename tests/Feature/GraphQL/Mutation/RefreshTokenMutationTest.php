<?php

namespace Tests\Feature\GraphQL\Mutation;

use JWTAuth;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;

class RefreshTokenMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    const REFRESH_TOKEN_QUERY = '
        mutation refreshToken($token: String!) {
            refreshToken(token: $token) {
                token
            }
        }
    ';
    
    /**
     * Test "refresh token" mutation.
     *
     * @return void
     */
    public function testRefreshTokenMutation()
    {
        $user = factory(User::class)->create();
        
        $expiredToken = JWTAuth::fromUser($user);
        
        JWTAuth::shouldReceive('refresh')->once();
        
        // Send dates to future.
        Carbon::setTestNow(Carbon::now()->addMinutes(config('jwt.ttl')));
        
        $query = self::REFRESH_TOKEN_QUERY;
        
        $params = [
            'token' => $expiredToken
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response has the expected object.
            ->assertJsonStructure([
                'data' => [
                    'refreshToken' => ['token']
                ]
            ]);
    }
}
