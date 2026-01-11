<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@library.com',
            'password' => bcrypt('password'),
            'can_borrow' => false,
            'role' => 'admin',
        ]);

        // Create member users as per TEST_CREDENTIALS.txt
        $member1 = User::create([
            'name' => 'Lauretta Jerde DDS',
            'email' => 'donnie.fay@example.com',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $member2 = User::create([
            'name' => 'Melody Hettinger I',
            'email' => 'corrine.stoltenberg@example.org',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $member3 = User::create([
            'name' => 'Nicholas Bednar',
            'email' => 'ora.romaguera@example.com',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $member4 = User::create([
            'name' => 'Ambrose Balistreri',
            'email' => 'denesik.william@example.org',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $member5 = User::create([
            'name' => 'Jeremie Bins',
            'email' => 'ohermann@example.net',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'can_borrow' => true,
            'role' => 'member',
        ]);

        $members = [$member1, $member2, $member3, $member4, $member5, $testUser];

        // Create sample books
        $book1 = Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'description' => 'A classic American novel about wealth and love in the Jazz Age.',
            'quantity' => 3,
            'available' => 2,
        ]);

        $book2 = Book::create([
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'isbn' => '9780061120084',
            'description' => 'A gripping tale of racial injustice in the American South.',
            'quantity' => 4,
            'available' => 3,
        ]);

        $book3 = Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'isbn' => '9780451524935',
            'description' => 'A dystopian novel about totalitarianism and surveillance.',
            'quantity' => 4,
            'available' => 4,
        ]);

        $book4 = Book::create([
            'title' => 'Pride and Prejudice',
            'author' => 'Jane Austen',
            'isbn' => '9780141040387',
            'description' => 'A romantic novel about family, marriage, and social class.',
            'quantity' => 2,
            'available' => 2,
        ]);

        $book5 = Book::create([
            'title' => 'The Catcher in the Rye',
            'author' => 'J.D. Salinger',
            'isbn' => '9780316769174',
            'description' => 'A coming-of-age story following a teenage boy in New York.',
            'quantity' => 3,
            'available' => 2,
        ]);

        // Create some sample borrowings matching TEST_CREDENTIALS.txt
        
        // Member 1 (Lauretta): The Great Gatsby - 1 active borrowing (belum overdue)
        Borrowing::create([
            'user_id' => $member1->id,
            'book_id' => $book1->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'return_date' => null,
            'status' => 'borrowed',
        ]);

        // Member 2 (Melody): To Kill a Mockingbird - 1 active borrowing (OVERDUE 2 hari)
        Borrowing::create([
            'user_id' => $member2->id,
            'book_id' => $book2->id,
            'borrow_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'return_date' => null,
            'status' => 'borrowed',
        ]);

        // Member 3 (Nicholas): Returned ON-TIME history
        Borrowing::create([
            'user_id' => $member3->id,
            'book_id' => $book1->id,
            'borrow_date' => now()->subDays(15)->toDateString(),
            'due_date' => now()->subDays(8)->toDateString(),
            'return_date' => now()->subDays(8)->toDateString(),
            'status' => 'returned',
        ]);

        // Member 4 (Ambrose): Returned ON-TIME history
        Borrowing::create([
            'user_id' => $member4->id,
            'book_id' => $book5->id,
            'borrow_date' => now()->subDays(20)->toDateString(),
            'due_date' => now()->subDays(13)->toDateString(),
            'return_date' => now()->subDays(13)->toDateString(),
            'status' => 'returned',
        ]);

        // Member 5 (Jeremie): The Great Gatsby - 1 active borrowing (OVERDUE 5 hari)
        Borrowing::create([
            'user_id' => $member5->id,
            'book_id' => $book1->id,
            'borrow_date' => now()->subDays(12)->toDateString(),
            'due_date' => now()->subDays(5)->toDateString(),
            'return_date' => null,
            'status' => 'borrowed',
        ]);

        // Test User: The Catcher in the Rye - 1 active borrowing (belum overdue)
        Borrowing::create([
            'user_id' => $testUser->id,
            'book_id' => $book5->id,
            'borrow_date' => now()->subDays(3)->toDateString(),
            'due_date' => now()->addDays(4)->toDateString(),
            'return_date' => null,
            'status' => 'borrowed',
        ]);
    }
}

