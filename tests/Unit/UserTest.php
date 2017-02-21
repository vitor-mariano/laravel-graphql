<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * Test if user is created successfully.
     *
     * @return void
     */
    public function testUserIsCreatedSuccessfully()
    {
        $user = factory(User::class)->create();
        
        $this->assertTrue($user->exists);
    }
}
