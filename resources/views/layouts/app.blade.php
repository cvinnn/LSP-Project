
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold">📚 Library System</h1>
                </div>
                <ul class="flex items-center space-x-6">
                    @auth
                        <li><a href="{{ route('books.index') }}" class="hover:text-blue-200">Catalog</a></li>
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('borrowings.index') }}" class="hover:text-blue-200">Borrowings</a></li>
                            <li><a href="{{ route('users.index') }}" class="hover:text-blue-200">Members</a></li>
                            <li><a href="{{ route('books.create') }}" class="hover:text-blue-200">Add Book</a></li>
                            <li><a href="{{ route('borrowings.create') }}" class="hover:text-blue-200">New Borrow</a></li>
                        @else
                            <li><a href="{{ route('borrowings.user', auth()->user()->id) }}" class="hover:text-blue-200">My Borrowings</a></li>
                        @endif
                        <li><span class="text-sm">{{ auth()->user()->name }} <span class="text-xs bg-blue-700 px-2 py-1 rounded">{{ ucfirst(auth()->user()->role) }}</span></span></li>
                        <li class="border-l border-blue-400 pl-6">
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="hover:text-blue-200" style="background:none;border:none;color:white;cursor:pointer;padding:0;">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                {{ session('warning') }}
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-gray-800 text-white text-center py-4 mt-12">
    </footer>
</body>
</html>
