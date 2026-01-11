@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">My Profile</h2>
    <p class="text-gray-600 mb-6">Your borrowing information</p>

    <!-- Member Info -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-600 p-6 rounded-lg mb-8">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-1">NAME</p>
                <p class="text-xl font-bold text-gray-800">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-1">EMAIL</p>
                <p class="text-xl font-bold text-gray-800">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-1">BORROWING STATUS</p>
                @if ($user->can_borrow)
                    <p class="text-xl font-bold text-green-600">✓ Active</p>
                @else
                    <p class="text-xl font-bold text-red-600">✗ Suspended</p>
                @endif
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold mb-1">MEMBER SINCE</p>
                <p class="text-xl font-bold text-gray-800">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    @if (!$user->can_borrow)
        <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-4 mb-6 rounded">
            <p><strong>⚠ Note:</strong> Your borrowing privileges have been suspended. Please contact the library staff.</p>
        </div>
    @endif

    <!-- Current Borrowings -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">📚 Currently Borrowed ({{ $activeBorrowings->count() }})</h3>
        
        @if ($activeBorrowings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($activeBorrowings as $borrowing)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow {{ $borrowing->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-lg font-bold text-gray-800 flex-1">{{ $borrowing->book->title }}</h4>
                            @if ($borrowing->isOverdue())
                                <span class="inline-block bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">OVERDUE</span>
                            @else
                                <span class="inline-block bg-green-600 text-white px-2 py-1 rounded text-xs font-bold">ACTIVE</span>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm mb-3">by {{ $borrowing->book->author }}</p>
                        <div class="text-sm text-gray-700 space-y-1 border-t pt-3">
                            <p><strong>Borrowed:</strong> {{ $borrowing->borrow_date->format('M d, Y') }}</p>
                            <p class="{{ $borrowing->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                <strong>Due:</strong> {{ $borrowing->due_date->format('M d, Y') }}
                                @if ($borrowing->isOverdue())
                                    <span class="text-red-600">({{ now()->diffInDays($borrowing->due_date) }} days overdue)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-4 rounded">
                <p>You haven't borrowed any books yet. Visit the <a href="{{ route('books.index') }}" class="text-blue-600 font-semibold hover:underline">catalog</a> to get started!</p>
            </div>
        @endif
    </div>

    <!-- Returned Books -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">✓ Returned Books ({{ $returnedBorrowings->count() }})</h3>
        
        @if ($returnedBorrowings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Book Title</th>
                            <th class="px-4 py-3 text-center text-gray-700 font-bold">Borrowed</th>
                            <th class="px-4 py-3 text-center text-gray-700 font-bold">Returned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnedBorrowings as $borrowing)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-800">
                                    <strong>{{ $borrowing->book->title }}</strong>
                                    <p class="text-sm text-gray-600">by {{ $borrowing->book->author }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $borrowing->borrow_date->format('M d') }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $borrowing->return_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-4 rounded">
                <p>No books returned yet.</p>
            </div>
        @endif
    </div>

    <div class="flex gap-4">
        <a href="{{ route('books.index') }}" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">
            Browse Catalog
        </a>
        <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">
            Back
        </a>
    </div>
</div>
@endsection
