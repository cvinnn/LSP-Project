@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">{{ $book->title }}</h2>

    <div class="mb-6">
        @if ($book->cover_image)
            <img src="{{ $book->cover_image }}" alt="Cover of {{ $book->title }}" class="w-48 h-64 object-cover rounded shadow">
        @else
            <div class="w-48 h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-sm">
                No Cover
            </div>
        @endif
    </div>
    
    <div class="mb-6">
        <p class="text-lg mb-2"><strong class="text-gray-700">Author:</strong> <span class="text-gray-600">{{ $book->author }}</span></p>
        <p class="text-lg mb-2"><strong class="text-gray-700">ISBN:</strong> <span class="text-gray-600">{{ $book->isbn }}</span></p>
        <p class="text-lg mb-2"><strong class="text-gray-700">Total Copies:</strong> <span class="text-blue-600 font-bold">{{ $book->quantity }}</span></p>
        <p class="text-lg mb-4"><strong class="text-gray-700">Available:</strong> <span class="text-green-600 font-bold">{{ $book->available }}</span></p>
    </div>

    @if ($book->description)
        <div class="mb-6 p-4 bg-gray-100 rounded">
            <h3 class="font-bold text-gray-800 mb-2">Description</h3>
            <p class="text-gray-700">{{ $book->description }}</p>
        </div>
    @endif

    <div class="mb-6">
        <h3 class="font-bold text-gray-800 mb-4">Borrowing History</h3>
        @if ($book->borrowings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-2 text-left">Member</th>
                            <th class="border p-2 text-left">Borrow Date</th>
                            <th class="border p-2 text-left">Due Date</th>
                            <th class="border p-2 text-left">Return Date</th>
                            <th class="border p-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($book->borrowings as $borrowing)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $borrowing->user->name }}</td>
                                <td class="border p-2">{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                                <td class="border p-2">{{ $borrowing->due_date->format('M d, Y') }}</td>
                                <td class="border p-2">{{ $borrowing->return_date?->format('M d, Y') ?? '-' }}</td>
                                <td class="border p-2">
                                    <span class="px-3 py-1 rounded text-white text-sm {{ $borrowing->status === 'returned' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">No borrowing history.</p>
        @endif
    </div>

    @if ($book->available == 1)
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <p><strong>🚨 Alert:</strong> Only 1 copy left! This is the last available copy.</p>
        </div>
    @elseif ($book->available <= 0)
        <div class="mb-4 p-4 bg-red-200 border-l-4 border-red-700 text-red-900">
            <p><strong>❌ Out of Stock:</strong> This book is currently unavailable.</p>
        </div>
    @endif

    <div class="flex gap-4">
        <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">Back to Catalog</a>

        @if (auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('books.edit', $book) }}" class="bg-yellow-600 text-white py-2 px-6 rounded hover:bg-yellow-700">Edit Book</a>
        @endif
    </div>
</div>
@endsection
