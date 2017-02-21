<?php

namespace Tests\Feature\GraphQL\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class UserQueryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test User return.
     *
     * @return void
     */
    public function testUserReturn()
    {
        $user = factory(User::class)->create();
        
        $query = '
            query findUser($id: Int!) {
                user(id: $id) {
                    name,
                    email
                }
            }
        ';
        
        $params = json_encode([
            'id' => $user->id
        ]);
        
        $response = $this->json('POST', '/graphql', compact('query', 'params'));
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]
            ]);
    }
}
