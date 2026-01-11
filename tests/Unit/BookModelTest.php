<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Tests\TestCase;

/**
 * BookModelTest - Unit tests for Book Model
 * 
 * Tests the Book model methods and relationships:
 * - Book creation and attributes
 * - Availability checking
 * - Relationships with Borrowing
 * 
 * @package Tests\Unit
 * @author Library Management System
 */
class BookModelTest extends TestCase
{

    /**
     * Test that a book can be created with valid attributes
     * 
     * @return void
     */
    public function test_book_can_be_created()
    {
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => 'TEST-ISBN-001',
            'description' => 'A test book',
            'quantity' => 5,
            'available' => 5,
        ]);

        $this->assertNotNull($book->id);
        $this->assertEquals('Test Book', $book->title);
        $this->assertEquals('Test Author', $book->author);
    }

    /**
     * Test that ISBN must be unique
     * 
     * @return void
     */
    public function test_isbn_is_unique()
    {
        Book::create([
            'title' => 'Book 1',
            'author' => 'Author 1',
            'isbn' => 'UNIQUE-ISBN',
            'quantity' => 1,
            'available' => 1,
        ]);

        $this->expectException(\Exception::class);
        
        Book::create([
            'title' => 'Book 2',
            'author' => 'Author 2',
            'isbn' => 'UNIQUE-ISBN', // Duplicate ISBN
            'quantity' => 1,
            'available' => 1,
        ]);
    }

    /**
     * Test isAvailable method returns true when available > 0
     * 
     * @return void
     */
    public function test_is_available_returns_true_when_copies_available()
    {
        $book = Book::create([
            'title' => 'Available Book',
            'author' => 'Test Author',
            'isbn' => 'AVAIL-001',
            'quantity' => 3,
            'available' => 2,
        ]);

        $this->assertTrue($book->isAvailable());
    }

    /**
     * Test isAvailable method returns false when available = 0
     * 
     * @return void
     */
    public function test_is_available_returns_false_when_no_copies_available()
    {
        $book = Book::create([
            'title' => 'Unavailable Book',
            'author' => 'Test Author',
            'isbn' => 'UNAVAIL-001',
            'quantity' => 3,
            'available' => 0,
        ]);

        $this->assertFalse($book->isAvailable());
    }

    /**
     * Test book has many borrowings
     * 
     * @return void
     */
    public function test_book_has_many_borrowings()
    {
        $book = Book::create([
            'title' => 'Popular Book',
            'author' => 'Test Author',
            'isbn' => 'POPULAR-001',
            'quantity' => 5,
            'available' => 3,
        ]);

        $this->assertNotNull($book->borrowings());
    }
}
