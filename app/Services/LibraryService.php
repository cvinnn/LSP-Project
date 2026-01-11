<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;

/**
 * LibraryService - Business logic for books, borrowings, and users
 */
class LibraryService
{
    // ==================== BOOK OPERATIONS ====================

    /**
     * Get all books
     */
    public function getAllBooks()
    {
        return Book::all();
    }

    /**
     * Get book by ID
     */
    public function getBook(int $id)
    {
        return Book::findOrFail($id);
    }

    /**
     * Create new book (set available = quantity)
     */
    public function createBook(array $data): Book
    {
        $data['available'] = $data['quantity'];
        return Book::create($data);
    }

    /**
     * Update book
     */
    public function updateBook(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    /**
     * Delete book
     */
    public function deleteBook(Book $book): bool
    {
        return $book->delete();
    }

    /**
     * Get books with available copies
     */
    public function getAvailableBooks()
    {
        return Book::where('available', '>', 0)->get();
    }

    // ==================== BORROWING OPERATIONS ====================

    /**
     * Record borrowing (validate permission, check availability, decrement available)
     */
    public function recordBorrowing(User $user, Book $book): Borrowing
    {
        if (!$user->can_borrow) {
            throw new \Exception('User is not authorized to borrow books');
        }

        if ($book->available <= 0) {
            throw new \Exception('Book is not available');
        }

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $book->decrement('available');
        return $borrowing;
    }

    /**
     * Process return (set return_date, mark returned, increment available)
     */
    public function procesReturn(Borrowing $borrowing): Borrowing
    {
        if ($borrowing->status === 'returned') {
            throw new \Exception('This book has already been returned');
        }

        $borrowing->update([
            'return_date' => now()->toDateString(),
            'status' => 'returned',
        ]);

        $borrowing->book->increment('available');
        return $borrowing;
    }

    /**
     * Get all borrowings
     */
    public function getAllBorrowings()
    {
        return Borrowing::with('user', 'book')->get();
    }

    /**
     * Check if borrowing is overdue (borrowed + past due_date)
     */
    public function isOverdue(Borrowing $borrowing): bool
    {
        return $borrowing->status === 'borrowed' && now()->isAfter($borrowing->due_date);
    }

    /**
     * Get books available for borrowing
     */
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableBooksForBorrowing()
    {
        return Book::where('available', '>', 0)->get();
    }

    /**
     * Get members with active borrowing permission
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBorrowingMembers()
    {
        return User::where('role', 'member')->where('can_borrow', true)->get();
    }

    // ==================== USER OPERATIONS ====================

    /**
     * Get all users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return User::withCount('borrowings')->get();
    }

    /**
     * Get single user by ID
     * 
     * @param int $id User ID
     * @return \App\Models\User User instance
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException When user not found
     */
    public function getUser(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Toggle user borrowing permission
     */
    public function toggleBorrowingPermission(User $user): User
    {
        $user->update(['can_borrow' => !$user->can_borrow]);
        return $user;
    }

    /**
     * Check if user can borrow
     */
    public function canBorrow(User $user): bool
    {
        return $user->can_borrow === true;
    }

    /**
     * Get all borrowing records for user
     */
    public function getUserBorrowings(User $user)
    {
        return $user->borrowings()->with('book')->get();
    }

    /**
     * Get active (unreturned) borrowings for user
     */
    public function getActiveBorrowings(User $user)
    {
        return $user->borrowings()->where('status', 'borrowed')->with('book')->get();
    }

    /**
     * Get returned borrowing records for user
     */
    public function getReturnedBorrowings(User $user)
    {
        return $user->borrowings()->where('status', 'returned')->with('book')->get();
    }
}
