<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * BorrowingController - Manage borrowing transactions and history
 */
class BorrowingController extends BaseController
{
    /**
     * Show borrowing list (all for admin, own for members)
     */
    public function index()
    {
        try {
            $user = auth()->user();
            
            // If member, show only their borrowings
            if ($user->role === 'member') {
                $borrowings = $user->borrowings()->with('book')->orderBy('borrow_date', 'desc')->get();
            } else {
                // Admin sees all borrowings
                $borrowings = Borrowing::with('user', 'book')->orderBy('borrow_date', 'desc')->get();
            }
            
            return view('borrowings.index', compact('borrowings'));
        } catch (\Exception $e) {
            \Log::error('Error fetching borrowing records: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading borrowing records');
        }
    }

    /**
     * Show user's borrowing history
     */
    public function userBorrowings(User $user)
    {
        try {
            $borrowings = $user->borrowings()->with('book')->orderBy('borrow_date', 'desc')->get();
            return view('borrowings.user', compact('user', 'borrowings'));
        } catch (\Exception $e) {
            \Log::error('Error fetching user borrowings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading borrowing history');
        }
    }

    /**
     * Show new borrowing form
     */
    public function create()
    {
        try {
            $users = User::where('can_borrow', true)->get();
            $books = Book::where('available', '>', 0)->get();
            return view('borrowings.create', compact('users', 'books'));
        } catch (\Exception $e) {
            \Log::error('Error loading borrowing form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading form');
        }
    }

    /**
     * Record new borrowing (validate permissions, check availability, decrement book count)
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'book_id' => 'required|exists:books,id',
                'borrow_date' => 'required|date',
            ]);

            // Get the user and book
            $user = User::find($validated['user_id']);
            $book = Book::find($validated['book_id']);

            // Check if user has borrowing permission
            if (!$user->can_borrow) {
                \Log::warning('User ' . $user->name . ' attempted to borrow but permission disabled');
                return redirect()->back()->with('error', $user->name . ' is not authorized to borrow books!');
            }

            // Check if book is available for borrowing
            if ($book->available <= 0) {
                \Log::warning('Attempted to borrow unavailable book: ' . $book->title);
                return redirect()->back()->with('error', 'Book is not available!');
            }

            // Create borrowing record
            $borrowing = Borrowing::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'borrow_date' => $validated['borrow_date'],
                'due_date' => \Carbon\Carbon::parse($validated['borrow_date'])->addDays(7)->toDateString(), // Due in 7 days from borrow date
                'status' => 'borrowed',
            ]);

            // Update book availability
            $book->decrement('available');

            \Log::info('Borrowing created: User ' . $validated['user_id'] . ' borrowed book ' . $book->title);
            return redirect()->route('borrowings.index')->with('success', 'Borrowing recorded successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error creating borrowing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error recording borrowing');
        }
    }

    /**
     * Mark book as returned (verify not already returned, update status, increment book count)
     */
    public function return(Borrowing $borrowing)
    {
        try {
            // Check if already returned
            if ($borrowing->status === 'returned') {
                \Log::warning('Attempted to return already-returned book: ' . $borrowing->book->title);
                return redirect()->back()->with('error', 'This book is already returned!');
            }

            // Update borrowing status
            $borrowing->update([
                'return_date' => now()->toDateString(),
                'status' => 'returned',
            ]);

            // Restore book availability
            $borrowing->book->increment('available');

            \Log::info('Book returned: ' . $borrowing->book->title . ' by user ' . $borrowing->user->name);
            return redirect()->route('borrowings.index')->with('success', 'Book returned successfully!');
        } catch (\Exception $e) {
            \Log::error('Error processing return: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error processing return');
        }
    }

    /**
     * Member borrow a book directly (quick borrow).
     * 
     * Allows a member to borrow a book directly from the catalog view
     * without admin intervention. Checks permission and availability.
     * 
     * HTTP Method: POST
     * Route: /borrow/{book}
     * 
     * @param \App\Models\Book $book The book to borrow
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function quickBorrow(Book $book)
    {
        try {
            $user = auth()->user();

            // Check if user has borrowing permission
            if (!$user->can_borrow) {
                return redirect()->back()->with('warning', 'You are not authorized to borrow books.');
            }

            // Check if book is available
            if ($book->available <= 0) {
                return redirect()->back()->with('warning', 'This book is not available right now.');
            }

            // Create borrowing record
            Borrowing::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => 'borrowed',
            ]);

            // Update book availability
            $book->decrement('available');

            \Log::info('Book borrowed by ' . $user->name . ': ' . $book->title);
            return redirect()->back()->with('success', 'Book borrowed successfully! Due date: ' . now()->addDays(7)->format('M d, Y'));
        } catch (\Exception $e) {
            \Log::error('Error borrowing book: ' . $e->getMessage());
            return redirect()->back()->with('warning', 'Error borrowing book');
        }
    }
}
