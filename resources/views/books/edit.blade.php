@extends('layouts.app')

@section('title', 'Edit Book')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Book</h2>

    <form action="{{ route('books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" required>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="author" class="block text-gray-700 font-bold mb-2">Author</label>
            <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" required>
            @error('author')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="isbn" class="block text-gray-700 font-bold mb-2">ISBN <span class="text-red-600">*</span></label>
            <input 
                type="text" 
                id="isbn" 
                name="isbn" 
                value="{{ old('isbn', $book->isbn) }}" 
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" 
                placeholder="e.g., 9780743273565"
                inputmode="numeric"
                maxlength="13"
                required
            >
            <p class="text-gray-500 text-xs mt-1">Must be exactly 13 numeric digits (e.g., 9780743273565)</p>
            @error('isbn')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-gray-700 font-bold mb-2">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $book->quantity) }}" min="1" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" required>
            @error('quantity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description <span class="text-red-600">*</span></label>
            <textarea 
                id="description" 
                name="description" 
                rows="5" 
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" 
                placeholder="Enter book description (minimum 10 characters)"
                required
            >{{ old('description', $book->description) }}</textarea>
            <p class="text-gray-500 text-xs mt-1">Minimum 10 characters required</p>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">Update Book</button>
            <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
