<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Hệ thống quản lý công việc</title>
    <!-- Application stylesheet -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation bar -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-xl font-bold">Hệ thống quản lý công việc</a>
            @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:underline">Đăng xuất</button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Main content container -->
    <div class="container mx-auto mt-6">
        <!-- Error message -->
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-lg shadow-sm animate-pulse">
                {{ session('error') }}
            </div>
        @endif

        <!-- Success message -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Warning message -->
        @if (session('warning'))
            <div class="bg-yellow-100 text-yellow-700 p-4 mb-4 rounded">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Content yield for child views -->
        @yield('content')
    </div>
</body>
</html>