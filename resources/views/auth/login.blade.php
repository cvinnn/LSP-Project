@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">📚 Library System</h2>
        <p class="text-center text-gray-600 mb-8">Sign in to your account</p>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" 
                    required
                >
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600" 
                    required
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 font-bold">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Don't have an account?</p>
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Create one now</a>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-200">
            <p class="text-sm text-gray-700 mb-2"><strong>Demo Accounts:</strong></p>
            <p class="text-xs text-gray-600 mb-1">Admin: admin@library.com / password</p>
            <p class="text-xs text-gray-600">Member: test@example.com / password</p>
        </div>
    </div>
</div>
@endsection
