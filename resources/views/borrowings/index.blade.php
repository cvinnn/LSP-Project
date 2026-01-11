@extends('layouts.app')

@section('title', auth()->user()->role === 'member' ? 'My Borrowing Records' : 'Borrowing Records')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">
        @if (auth()->user()->role === 'member')
            My Borrowing Records
        @else
            All Borrowing Records
        @endif
    </h2>

    @php
        $user = auth()->user();
        $dueReminders = ($user && $user->role === 'member') 
            ? $borrowings
                ->where('status', 'borrowed')
                ->filter(function($b) { return $b->due_date <= now()->addDays(3) && $b->due_date > now(); })
            : collect([]);
    @endphp

    @if ($dueReminders->count() > 0)
        <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800">
            <p class="font-bold">🔔 Reminder: Return Due Soon</p>
            <ul class="mt-2 ml-4 list-disc">
                @foreach ($dueReminders as $reminder)
                    <li>{{ $reminder->book->title }} - Due on {{ $reminder->due_date->format('M d, Y') }} ({{ $reminder->due_date->diffInDays(now()) }} days left)</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($borrowings->count() > 0)
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        @if (auth()->user()->role === 'admin')
                            <th class="px-6 py-3 text-left">Member</th>
                        @endif
                        <th class="px-6 py-3 text-left">Book</th>
                        <th class="px-6 py-3 text-left">Borrow Date</th>
                        <th class="px-6 py-3 text-left">Due Date</th>
                        <th class="px-6 py-3 text-left">Return Date</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        @if (auth()->user()->role === 'admin')
                            <th class="px-6 py-3 text-left">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $borrowing)
                        <tr class="border-b hover:bg-gray-50">
                            @if (auth()->user()->role === 'admin')
                                <td class="px-6 py-4">{{ $borrowing->user->name }}</td>
                            @endif
                            <td class="px-6 py-4">{{ $borrowing->book->title }}</td>
                            <td class="px-6 py-4">{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="{{ $borrowing->isOverdue() && $borrowing->status === 'borrowed' ? 'text-red-600 font-bold' : '' }}">
                                    {{ $borrowing->due_date->format('M d, Y') }}
                                    @if ($borrowing->isOverdue() && $borrowing->status === 'borrowed')
                                        <span class="text-red-600">(OVERDUE)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $borrowing->status === 'returned' && $borrowing->return_date > $borrowing->due_date ? 'text-orange-600 font-bold' : '' }}">
                                    {{ $borrowing->return_date?->format('M d, Y') ?? '-' }}
                                    @if ($borrowing->status === 'returned' && $borrowing->return_date > $borrowing->due_date)
                                        <span class="text-orange-600">(LATE)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded text-white text-sm font-bold {{ $borrowing->status === 'returned' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            @if (auth()->user()->role === 'admin')
                                <td class="px-6 py-4">
                                    @if ($borrowing->status === 'borrowed')
                                        <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Return</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            <p>No borrowing records yet.</p>
        </div>
    @endif
</div>
@endsection
