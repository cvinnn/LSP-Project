<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

/**
 * BookController - Manage book catalog (CRUD operations)
 */
class BookController extends BaseController
{
    /**
     * Show book catalog with stock status badges
     */
    public function index()
    {
        try {
            $books = Book::all();
            return view('books.index', compact('books'));
        } catch (\Exception $e) {
            \Log::error('Error fetching books: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading book catalog');
        }
    }

    /**
     * Show single book detail
     * 
     * Tampilkan detail buku termasuk deskripsi, author, ISBN, stock status
     * 
     * @param \App\Models\Book $book
     * @return \Illuminate\View\View books.show with book details
     */
    public function show(Book $book)
    {
        try {
            return view('books.show', compact('book'));
        } catch (\Exception $e) {
            \Log::error('Error displaying book: ' . $e->getMessage());
            return redirect()->route('books.index')->with('error', 'Error loading book details');
        }
    }

    /**
     * Show form to create new book (Admin only)
     * 
     * @return \Illuminate\View\View books.create form
     */
    public function create()
    {
        try {
            return view('books.create');
        } catch (\Exception $e) {
            \Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading form');
        }
    }

    /**
     * Store new book (Admin only)
     * 
     * Validasi:
     * - Title, Author: required, max 255
     * - ISBN: required, 13 digits, unique
     * - Description: required, min 10 chars
     * - Quantity: required, min 1, integer
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|unique:books|numeric|digits:13',
                'description' => 'required|string|min:10',
                'cover_image' => ['nullable', 'url', 'max:2048', 'regex:/\.(jpe?g|png|gif|webp)(\?.*)?$/i'],
                'quantity' => 'required|integer|min:1',
            ], [
                'isbn.numeric' => 'ISBN must contain only numbers (0-9)',
                'isbn.digits' => 'ISBN must be exactly 13 digits',
                'description.required' => 'Description is required',
                'description.min' => 'Description must be at least 10 characters',
                'cover_image.regex' => 'Cover URL must end with jpg, jpeg, png, gif, or webp',
            ]);

            // Set available copies equal to quantity for new books
            $validated['available'] = $validated['quantity'];
            
            // Create the book record
            Book::create($validated);

            \Log::info('Book created: ' . $validated['title']);
            return redirect()->route('books.index')->with('success', 'Book added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating book: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating book')->withInput();
        }
    }

    // Form edit buku
    public function edit(Book $book)
    {
        try {
            return view('books.edit', compact('book'));
        } catch (\Exception $e) {
            \Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading form');
        }
    }

    // Update data buku
    public function update(Request $request, Book $book)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|numeric|digits:13|unique:books,isbn,' . $book->id,
                'description' => 'required|string|min:10',
                'cover_image' => ['nullable', 'url', 'max:2048', 'regex:/\.(jpe?g|png|gif|webp)(\?.*)?$/i'],
                'quantity' => 'required|integer|min:1',
            ], [
                'isbn.numeric' => 'ISBN must contain only numbers (0-9)',
                'isbn.digits' => 'ISBN must be exactly 13 digits',
                'description.required' => 'Description is required',
                'description.min' => 'Description must be at least 10 characters',
                'cover_image.regex' => 'Cover URL must end with jpg, jpeg, png, gif, or webp',
            ]);

            // Hitung berapa buku yang sedang dipinjam
            $borrowed = $book->quantity - $book->available;

            // Validasi: quantity tidak boleh lebih kecil dari yang dipinjam
            if ($validated['quantity'] < $borrowed) {
                \Log::warning('Attempted to reduce quantity below borrowed amount: ' . $book->title);
                return redirect()->back()->with('error', 
                    'Cannot reduce total quantity to ' . $validated['quantity'] . '! ' .
                    'There are currently ' . $borrowed . ' copies being borrowed. ' .
                    'Minimum quantity must be ' . $borrowed . ' or higher.'
                )->withInput();
            }

            // Update quantity dan available
            // Jika quantity berubah, update available sesuai dengan buku yang dipinjam
            $validated['available'] = $validated['quantity'] - $borrowed;

            $book->update($validated);

            \Log::info('Book updated: ' . $book->title . ' (quantity: ' . $validated['quantity'] . ', available: ' . $validated['available'] . ')');
            return redirect()->route('books.index')->with('success', 'Book updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating book: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating book')->withInput();
        }
    }

    // Hapus buku, tapi cek dulu apakah ada peminjaman aktif
    public function destroy(Book $book)
    {
        try {
            // Check if book has active borrowings
            $activeBorrowings = $book->borrowings()->where('status', 'borrowed')->count();
            
            if ($activeBorrowings > 0) {
                \Log::warning('Attempted to delete book with active borrowings: ' . $book->title);
                return redirect()->back()->with('warning', 'Cannot delete this book. There are still ' . $activeBorrowings . ' active borrowing(s). Please wait until all copies are returned.');
            }
            
            $bookTitle = $book->title;
            $book->delete();
            
            \Log::info('Book deleted: ' . $bookTitle);
            return redirect()->route('books.index')->with('success', 'Book deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting book: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting book');
        }
    }
}
