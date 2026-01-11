@extends('layouts.app')

@section('title', $user->name . ' - Borrowing History')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $user->name }}</h2>
    <p class="text-gray-600 mb-6">Email: {{ $user->email }}</p>

    @if ($borrowings->count() > 0)
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">Book</th>
                        <th class="px-6 py-3 text-left">Author</th>
                        <th class="px-6 py-3 text-left">Borrow Date</th>
                        <th class="px-6 py-3 text-left">Due Date</th>
                        <th class="px-6 py-3 text-left">Return Date</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $borrowing)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $borrowing->book->title }}</td>
                            <td class="px-6 py-4">{{ $borrowing->book->author }}</td>
                            <td class="px-6 py-4">{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $borrowing->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $borrowing->return_date?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded text-white text-sm font-bold {{ $borrowing->status === 'returned' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            <p>No borrowing history for this member.</p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">Back to Catalog</a>
    </div>
</div>
@endsection
