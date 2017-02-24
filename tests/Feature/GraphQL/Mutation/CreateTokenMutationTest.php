<?php

namespace Tests\Feature\GraphQL\Mutation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use JWTAuth;

class CreateTokenMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    const CREATE_TOKEN_QUERY = '
        mutation createToken(
            $email: String!,
            $password: String!
        ) {
            createToken(
                email: $email,
                password: $password
            ) {
                token
            }
        }
    ';
    
    /**
     * Test "create token" mutation.
     *
     * @return void
     */
    public function testCreateTokenMutation()
    {
        $user = factory(User::class)->create();
        
        $query = self::CREATE_TOKEN_QUERY;
        
        $params = [
            'email' => $user->email,
            'password' => 'secret'
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response has the expected object.
            ->assertJsonFragment([
                'createToken' => [
                    'token' => JWTAuth::fromUser($user)
                ]
            ]);
    }
    
    /**
     * Test if credentials are valid.
     *
     * @return void
     */
    public function testShouldNotCreateTokenWithInvalidCredentials()
    {
        $fakeUser = factory(User::class)->make();
        
        $query = self::CREATE_TOKEN_QUERY;
        
        $params = [
            'email' => $fakeUser->email,
            'password' => 'fakepassword'
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But there is no user with these credentials.
            ->assertJsonStructure([
                'data' => [
                    'createToken' => ['token']
                ]
            ])
            ->assertJsonFragment([
                'createToken' => [
                    'token' => null
                ]
            ]);
    }
    
    /**
     * Test if important fields are filled.
     *
     * @return [type] [description]
     */
    public function testFieldsAreRequired()
    {
        $query = '
            mutation {
                createToken(
                    email: "",
                    password: ""
                ) {
                    token
                }
            }
        ';
        
        $response = $this->graphql($query);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But some fileds are missing.
            ->assertJsonFragment([
                'validation' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }
    
    /**
     * Test if password length has at least 6 characters.
     *
     * @return void
     */
    public function testPasswordMustBeAtLeast6Characters()
    {
        $user = factory(User::class)->make();
        
        $query = self::CREATE_TOKEN_QUERY;
        
        $params = [
            'email' => $user->email,
            'password' => '12345'
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But password length in too small.
            ->assertJsonFragment([
                'validation' => [
                    'password' => ['The password must be at least 6 characters.']
                ]
            ]);
    }
    
    /**
     * Test if email format is valid.
     *
     * @return void
     */
    public function testEmailIsInvalid()
    {
        $query = self::CREATE_TOKEN_QUERY;
        
        $params = [
            'email' => 'invalidemail',
            'password' => 'secret'
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But email format is invalid.
            ->assertJsonFragment([
                'validation' => [
                    'email' => ['The email must be a valid email address.']
                ]
            ]);
    }
}
