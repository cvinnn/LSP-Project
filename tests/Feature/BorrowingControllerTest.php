<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Tests\TestCase;

/**
 * BorrowingControllerTest - Feature tests for Borrowing Controller
 * 
 * Tests all BorrowingController methods via HTTP requests:
 * - Index (view all borrowings)
 * - Create (display create form)
 * - Store (record new borrowing)
 * - Return (mark book as returned)
 * - User borrowings (view user's history)
 * 
 * @package Tests\Feature
 * @author Library Management System
 */
class BorrowingControllerTest extends TestCase
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
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => 'TEST-BOOK-001',
            'quantity' => 5,
            'available' => 5,
        ]);
    }

    /**
     * Test viewing all borrowing records
     * 
     * @return void
     */
    public function test_can_view_borrowing_records()
    {
        $response = $this->get('/borrowings');

        $response->assertStatus(200);
        $response->assertViewIs('borrowings.index');
    }

    /**
     * Test displaying borrowing creation form
     * 
     * @return void
     */
    public function test_can_view_create_borrowing_form()
    {
        $response = $this->get('/borrowings/create');

        $response->assertStatus(200);
        $response->assertViewIs('borrowings.create');
    }

    /**
     * Test recording a new borrowing
     * 
     * @return void
     */
    public function test_can_record_borrowing()
    {
        $initialAvailable = $this->book->available;

        $data = [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ];

        $response = $this->post('/borrowings', $data);

        $response->assertRedirect('/borrowings');
        
        // Check borrowing was created
        $this->assertDatabaseHas('borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'status' => 'borrowed',
        ]);

        // Check availability was decremented
        $this->book->refresh();
        $this->assertEquals($initialAvailable - 1, $this->book->available);
    }

    /**
     * Test cannot borrow unavailable book
     * 
     * @return void
     */
    public function test_cannot_borrow_unavailable_book()
    {
        // Make book unavailable
        $this->book->update(['available' => 0]);

        $data = [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ];

        $response = $this->post('/borrowings', $data);

        $response->assertSessionHas('error');
    }

    /**
     * Test cannot borrow with invalid user
     * 
     * @return void
     */
    public function test_cannot_borrow_with_invalid_user()
    {
        $data = [
            'user_id' => 9999, // Non-existent user
            'book_id' => $this->book->id,
        ];

        $response = $this->post('/borrowings', $data);

        $response->assertSessionHasErrors();
    }

    /**
     * Test returning a borrowed book
     * 
     * @return void
     */
    public function test_can_return_borrowed_book()
    {
        // Create a borrowing
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $this->book->decrement('available');
        $initialAvailable = $this->book->available;

        // Return the book
        $response = $this->put("/borrowings/{$borrowing->id}/return");

        $response->assertRedirect('/borrowings');

        // Check borrowing status changed
        $borrowing->refresh();
        $this->assertEquals('returned', $borrowing->status);
        $this->assertNotNull($borrowing->return_date);

        // Check availability was incremented
        $this->book->refresh();
        $this->assertEquals($initialAvailable + 1, $this->book->available);
    }

    /**
     * Test cannot return already returned book
     * 
     * @return void
     */
    public function test_cannot_return_already_returned_book()
    {
        // Create and complete a borrowing
        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrow_date' => now()->subDays(7)->toDateString(),
            'due_date' => now()->toDateString(),
            'return_date' => now()->toDateString(),
            'status' => 'returned',
        ]);

        $response = $this->put("/borrowings/{$borrowing->id}/return");

        $response->assertSessionHas('error');
    }

    /**
     * Test viewing user's borrowing history
     * 
     * @return void
     */
    public function test_can_view_user_borrowing_history()
    {
        $response = $this->get("/users/{$this->user->id}/borrowings");

        $response->assertStatus(200);
        $response->assertViewIs('borrowings.user');
    }

    /**
     * Test borrowing validation requires user_id
     * 
     * @return void
     */
    public function test_borrowing_requires_user_id()
    {
        $data = [
            'book_id' => $this->book->id,
        ];

        $response = $this->post('/borrowings', $data);

        $response->assertSessionHasErrors('user_id');
    }

    /**
     * Test borrowing validation requires book_id
     * 
     * @return void
     */
    public function test_borrowing_requires_book_id()
    {
        $data = [
            'user_id' => $this->user->id,
        ];

        $response = $this->post('/borrowings', $data);

        $response->assertSessionHasErrors('book_id');
    }
}
