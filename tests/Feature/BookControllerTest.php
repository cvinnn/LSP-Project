<?php

namespace Tests\Feature;

use App\Models\Book;
use Tests\TestCase;

/**
 * BookControllerTest - Feature tests for Book Controller
 * 
 * Tests all BookController methods via HTTP requests:
 * - Index (view all books)
 * - Show (view book details)
 * - Create (display create form)
 * - Store (save new book)
 * - Edit (display edit form)
 * - Update (update book)
 * - Destroy (delete book)
 * 
 * @package Tests\Feature
 * @author Library Management System
 */
class BookControllerTest extends TestCase
{
    /**
     * Test viewing book catalog (index)
     * 
     * @return void
     */
    public function test_can_view_book_catalog()
    {
        $books = Book::factory(3)->create();

        $response = $this->get('/books');

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
    }

    /**
     * Test viewing a specific book
     * 
     * @return void
     */
    public function test_can_view_book_details()
    {
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => 'TEST-001',
            'description' => 'A test book',
            'quantity' => 5,
            'available' => 5,
        ]);

        $response = $this->get("/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
    }

    /**
     * Test displaying book creation form
     * 
     * @return void
     */
    public function test_can_view_create_book_form()
    {
        $response = $this->get('/books/create');

        $response->assertStatus(200);
        $response->assertViewIs('books.create');
    }

    /**
     * Test creating a new book
     * 
     * @return void
     */
    public function test_can_create_book()
    {
        $data = [
            'title' => 'New Book',
            'author' => 'New Author',
            'isbn' => 'NEW-ISBN-001',
            'description' => 'A new book for testing',
            'quantity' => 5,
        ];

        $response = $this->post('/books', $data);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
            'author' => 'New Author',
            'isbn' => 'NEW-ISBN-001',
        ]);
    }

    /**
     * Test book creation fails with invalid data
     * 
     * @return void
     */
    public function test_cannot_create_book_with_invalid_data()
    {
        $data = [
            'title' => '', // Missing title
            'author' => 'Author',
            'isbn' => 'ISBN-001',
            'quantity' => 5,
        ];

        $response = $this->post('/books', $data);

        $response->assertSessionHasErrors();
    }

    /**
     * Test book creation fails with duplicate ISBN
     * 
     * @return void
     */
    public function test_cannot_create_book_with_duplicate_isbn()
    {
        Book::create([
            'title' => 'Book 1',
            'author' => 'Author 1',
            'isbn' => 'DUPLICATE-ISBN',
            'quantity' => 5,
            'available' => 5,
        ]);

        $data = [
            'title' => 'Book 2',
            'author' => 'Author 2',
            'isbn' => 'DUPLICATE-ISBN',
            'quantity' => 5,
        ];

        $response = $this->post('/books', $data);

        $response->assertSessionHasErrors();
    }

    /**
     * Test viewing book edit form
     * 
     * @return void
     */
    public function test_can_view_edit_book_form()
    {
        $book = Book::create([
            'title' => 'Editable Book',
            'author' => 'Editable Author',
            'isbn' => 'EDIT-001',
            'quantity' => 5,
            'available' => 5,
        ]);

        $response = $this->get("/books/{$book->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('books.edit');
    }

    /**
     * Test updating a book
     * 
     * @return void
     */
    public function test_can_update_book()
    {
        $book = Book::create([
            'title' => 'Original Title',
            'author' => 'Original Author',
            'isbn' => 'UPDATE-001',
            'quantity' => 5,
            'available' => 5,
        ]);

        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'isbn' => 'UPDATE-001',
            'quantity' => 10,
        ];

        $response = $this->put("/books/{$book->id}", $data);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
            'author' => 'Updated Author',
        ]);
    }

    /**
     * Test deleting a book
     * 
     * @return void
     */
    public function test_can_delete_book()
    {
        $book = Book::create([
            'title' => 'Deletable Book',
            'author' => 'Deletable Author',
            'isbn' => 'DELETE-001',
            'quantity' => 5,
            'available' => 5,
        ]);

        $response = $this->delete("/books/{$book->id}");

        $response->assertRedirect('/books');
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
