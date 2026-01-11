@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Library Members</h2>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="px-6 py-3 text-left text-gray-700 font-bold">Name</th>
                    <th class="px-6 py-3 text-left text-gray-700 font-bold">Email</th>
                    <th class="px-6 py-3 text-center text-gray-700 font-bold">Borrowing Status</th>
                    <th class="px-6 py-3 text-center text-gray-700 font-bold">Active Borrowed</th>
                    <th class="px-6 py-3 text-center text-gray-700 font-bold">Total Borrowings</th>
                    <th class="px-6 py-3 text-center text-gray-700 font-bold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $activeBorrowings = $user->borrowings()->where('status', 'borrowed')->count();
                    @endphp
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($user->can_borrow)
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    ✓ Allowed
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    ✗ Blocked
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-700 font-semibold">{{ $activeBorrowings }}</td>
                        <td class="px-6 py-4 text-center text-gray-700 font-semibold">{{ $user->borrowings_count }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('users.show', $user) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                    View
                                </a>
                                @if ($user->role === 'member')
                                    <form action="{{ route('users.toggle-permission', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        @if ($user->can_borrow)
                                            @if ($activeBorrowings > 0)
                                                <button type="submit" class="bg-gray-400 text-white px-3 py-1 rounded text-sm cursor-not-allowed" disabled title="User has active borrowings">
                                                    Block
                                                </button>
                                            @else
                                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm" onclick="return confirm('Block this user from borrowing?')">
                                                    Block
                                                </button>
                                            @endif
                                        @else
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm" onclick="return confirm('Allow this user to borrow?')">
                                                Allow
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-600">No members found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('books.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded hover:bg-gray-700">
            Back to Books
        </a>
    </div>
</div>
@endsection
