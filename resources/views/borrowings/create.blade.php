@extends('layouts.app')

@section('title', 'New Borrowing Record')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Record a New Borrowing</h2>

    <form action="{{ route('borrowings.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="user_id" class="block text-gray-700 font-bold mb-2">Member</label>
            <select id="user_id" name="user_id" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" required>
                <option value="">Select a member...</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="book_id" class="block text-gray-700 font-bold mb-2">Book (Available Only)</label>
            <select id="book_id" name="book_id" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" required>
                <option value="">Select a book...</option>
                @foreach ($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                        {{ $book->title }} by {{ $book->author }} (Available: {{ $book->available }})
                    </option>
                @endforeach
            </select>
            @error('book_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="borrow_date" class="block text-gray-700 font-bold mb-2">Borrow Date</label>
            <input type="date" id="borrow_date" value="{{ date('Y-m-d') }}" disabled class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 cursor-not-allowed focus:outline-none">
            <input type="hidden" name="borrow_date" value="{{ date('Y-m-d') }}">
            @error('borrow_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
            <p><strong>Note:</strong> The book will be due in 7 days from the borrow date.</p>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">Record Borrowing</button>
            <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
