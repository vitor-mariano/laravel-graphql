<?php

namespace Tests\Feature\GraphQL\Mutation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class CreateUserMutationTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Common "create user" query.
     *
     * @var string
     */
    const CREATE_USER_QUERY = '
        mutation createUser(
            $name: String!,
            $email: String!,
            $password: String!
        ) {
            createUser(
                name: $name,
                email: $email,
                password: $password
            ) {
                name,
                email
            }
        }
    ';
    
    /**
     * Test "create user" mutation.
     *
     * @return void
     */
    public function testCreateUserMutation()
    {
        $user = factory(User::class)->make([
            'password' => 'secret'
        ]);
        
        $query = self::CREATE_USER_QUERY;
        
        $params = $this->getParams($user);
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // Assert response has the expected object.
            ->assertJsonFragment([
                'createUser' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        
        $persistedUser = User::where('email', $user->email)->first();
        
        // Assert user was persisted into database.
        $this->assertInstanceOf(User::class, $persistedUser);
        
        // Assert password was hashed before save.
        $this->assertNotEquals($user->password, $persistedUser->password);
    }
    
    /**
     * Test if important fields are required.
     *
     * @return void
     */
    public function testFieldsAreRequired()
    {
        $query = '
            mutation {
                createUser(
                    name: "",
                    email: "",
                    password: ""
                ) {
                    name,
                    email
                }
            }
        ';
        
        $response = $this->graphql($query);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But some fields are missing.
            ->assertJsonFragment([
                'validation' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ]
            ]);
    }
    
    /**
     * Test if email format is invalid.
     *
     * @return void
     */
    public function testEmailIsInvalid()
    {
        $user = factory(User::class)->make([
            'email' => 'invalid-email'
        ]);
        
        $query = self::CREATE_USER_QUERY;
        
        $params = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password
        ];
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But the email format is invalid.
            ->assertJsonFragment([
                'validation' => [
                    'email' => ['The email must be a valid email address.']
                ]
            ]);
    }
    
    /**
     * Test if the given email was already been taken.
     *
     * @return void
     */
    public function testEmailIsUnique()
    {
        $user = factory(User::class)->create();
        
        $impostor = factory(User::class)->make([
            'email' => $user->email
        ]);
        
        $query = self::CREATE_USER_QUERY;
        
        $params = $this->getParams($impostor);
        
        $response = $this->graphql($query, $params);
        
        $response
            // Assert response is OK.
            ->assertStatus(200)
            
            // But the email was already been taken.
            ->assertJsonFragment([
                'validation' => [
                    'email' => ['The email has already been taken.']
                ]
            ]);
    }
    
    /**
     * Test if password is greater than 5.
     *
     * @return void
     */
    public function testPasswordMustBeAtLeast6Characters()
    {
        $user = factory(User::class)->make([
            'password' => '12345'
        ]);
        
        $query = self::CREATE_USER_QUERY;
        
        $params = $this->getParams($user);
        
        $request = $this->graphql($query, $params);
        
        $request
            ->assertStatus(200)
            ->assertJsonFragment([
                'validation' => [
                    'password' => ['The password must be at least 6 characters.']
                ]
            ]);
    }
    
    /**
     * Fill params array with model data.
     *
     * @param  User $user
     * @return void
     */
    protected function getParams($user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password
        ];
    }
}
