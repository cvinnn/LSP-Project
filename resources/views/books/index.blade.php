@extends('layouts.app')

@section('title', 'Book Catalog')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Library Catalog</h2>
    
    @if ($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($books as $book)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                        @if ($book->cover_image)
                            <img src="{{ $book->cover_image }}" alt="Cover of {{ $book->title }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-gray-400 text-sm">No Cover</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $book->title }}</h3>
                        <p class="text-gray-600 mb-1"><strong>Author:</strong> {{ $book->author }}</p>
                        <p class="text-gray-600 mb-1"><strong>ISBN:</strong> {{ $book->isbn }}</p>
                        <div class="flex items-center gap-2 mb-1">
                            <span><strong>Available:</strong> <span class="text-blue-600 font-bold">{{ $book->available }}/{{ $book->quantity }}</span></span>
                            @if ($book->available == 1)
                                <span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded font-bold">⚠️ Last Copy!</span>
                            @elseif ($book->available <= 0)
                                <span class="px-2 py-1 text-xs bg-red-300 text-red-900 rounded font-bold">Out of Stock</span>
                            @endif
                        </div>
                        
                        @if ($book->description)
                            <p class="text-gray-700 mt-4 text-sm">{{ Str::limit($book->description, 100) }}</p>
                        @endif

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('books.show', $book) }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 text-center">View</a>
                            @if (auth()->check() && auth()->user()->role === 'admin')
                                <a href="{{ route('books.edit', $book) }}" class="flex-1 bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700 text-center">Edit</a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>No books in the catalog. Add some books to get started!</p>
        </div>
    @endif
</div>
@endsection
