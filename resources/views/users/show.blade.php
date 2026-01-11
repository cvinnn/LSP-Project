@extends('layouts.app')

@section('title', $user->name . ' - Borrowing History')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h2>
            <p class="text-gray-600">{{ $user->email }}</p>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-blue-600">{{ $borrowings->count() }}</div>
            <p class="text-gray-600">Total Borrowings</p>
        </div>
    </div>

    <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
        <p class="text-gray-800">
            <strong>Borrowing Status:</strong>
            @if ($user->can_borrow)
                <span class="text-green-600 font-semibold">✓ Allowed to borrow</span>
            @else
                <span class="text-red-600 font-semibold">✗ Blocked from borrowing</span>
            @endif
        </p>
    </div>

    <h3 class="text-xl font-bold text-gray-800 mb-4">Borrowing History</h3>

    @if ($borrowings->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-300">
                        <th class="px-6 py-3 text-left text-gray-700 font-bold">Book Title</th>
                        <th class="px-6 py-3 text-left text-gray-700 font-bold">Author</th>
                        <th class="px-6 py-3 text-center text-gray-700 font-bold">Borrowed Date</th>
                        <th class="px-6 py-3 text-center text-gray-700 font-bold">Due Date</th>
                        <th class="px-6 py-3 text-center text-gray-700 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $borrowing)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-800 font-semibold">{{ $borrowing->book->title }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $borrowing->book->author }}</td>
                            <td class="px-6 py-4 text-center text-gray-700">{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center text-gray-700">{{ $borrowing->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($borrowing->status === 'returned')
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        Returned
                                    </span>
                                @else
                                    @if ($borrowing->isOverdue())
                                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            ⚠ Overdue
                                        </span>
                                    @else
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            Active
                                        </span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-4 rounded">
            <p>No borrowing history yet.</p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('users.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">
            Back to Members
        </a>
    </div>
</div>
@endsection
