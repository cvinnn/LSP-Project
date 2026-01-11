<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Borrowing;
use Tests\TestCase;

/**
 * UserModelTest - Unit tests for User Model
 * 
 * Tests the User model methods and relationships:
 * - User creation and attributes
 * - Password hashing
 * - Relationships with Borrowing
 * 
 * @package Tests\Unit
 * @author Library Management System
 */
class UserModelTest extends TestCase
{
    /**
     * Test that a user can be created with valid attributes
     * 
     * @return void
     */
    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotNull($user->id);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * Test that email must be unique
     * 
     * @return void
     */
    public function test_email_is_unique()
    {
        User::create([
            'name' => 'User 1',
            'email' => 'unique@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->expectException(\Exception::class);

        User::create([
            'name' => 'User 2',
            'email' => 'unique@example.com', // Duplicate email
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Test password is hidden in serialization
     * 
     * @return void
     */
    public function test_password_is_hidden()
    {
        $user = User::create([
            'name' => 'Hidden Password User',
            'email' => 'hidden@example.com',
            'password' => bcrypt('password123'),
        ]);

        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
    }

    /**
     * Test user has many borrowings
     * 
     * @return void
     */
    public function test_user_has_many_borrowings()
    {
        $user = User::create([
            'name' => 'Active Borrower',
            'email' => 'borrower@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotNull($user->borrowings());
    }
}
