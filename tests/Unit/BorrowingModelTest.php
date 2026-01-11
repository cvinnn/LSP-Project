<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * BorrowingModelTest - Unit tests for Borrowing Model
 * 
 * Tests the Borrowing model methods and relationships:
 * - Borrowing creation
 * - Overdue detection
 * - Relationships with User and Book
 * - Date casting
 * 
 * @package Tests\Unit
 * @author Library Management System
 */
class BorrowingModelTest extends TestCase
{
    protected $user;
    protected $book;

    /**
     * Set up test data
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test Borrower',
            'email' => 'borrower@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => 'TEST-ISBN-123',
            'quantity' => 5,
            'available' => 4,
        ]);
    }

    /**
     * Test that a borrowing can be created
     * 
     * @return void
     */
    public function test_borrowing_can_be_created()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->assertNotNull($borrowing->id);
        $this->assertEquals('borrowed', $borrowing->status);
    }

    /**
     * Test isOverdue returns false for recent borrowing
     * 
     * @return void
     */
    public function test_is_overdue_returns_false_for_current_borrowing()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->assertFalse($borrowing->isOverdue());
    }

    /**
     * Test isOverdue returns true for past due date
     * 
     * @return void
     */
    public function test_is_overdue_returns_true_for_overdue_borrowing()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(), // Overdue
            'status' => 'borrowed',
        ]);

        $this->assertTrue($borrowing->isOverdue());
    }

    /**
     * Test isOverdue returns false for returned books
     * 
     * @return void
     */
    public function test_is_overdue_returns_false_for_returned_book()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'return_date' => now()->subDays(2)->toDateString(),
            'status' => 'returned',
        ]);

        $this->assertFalse($borrowing->isOverdue());
    }

    /**
     * Test borrowing belongs to user
     * 
     * @return void
     */
    public function test_borrowing_belongs_to_user()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->assertNotNull($borrowing->user());
        $this->assertEquals($this->user->id, $borrowing->user->id);
    }

    /**
     * Test borrowing belongs to book
     * 
     * @return void
     */
    public function test_borrowing_belongs_to_book()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->assertNotNull($borrowing->book());
        $this->assertEquals($this->book->id, $borrowing->book->id);
    }

    /**
     * Test dates are cast properly
     * 
     * @return void
     */
    public function test_dates_are_cast_to_carbon()
    {
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->assertInstanceOf(Carbon::class, $borrowing->borrow_date);
        $this->assertInstanceOf(Carbon::class, $borrowing->due_date);
    }
}
