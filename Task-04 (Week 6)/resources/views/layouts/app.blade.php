<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .flash-success {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .flash-error {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f9fafb;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="email"],
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-group .is-invalid {
            border-color: #ef4444;
        }

        .form-group .field-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .validation-summary {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .validation-summary ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }

        .form-actions {
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .supplier-badge {
            display: inline-block;
            background-color: #e5e7eb;
            padding: 2px 8px;
            margin: 2px;
            border-radius: 4px;
            font-size: 12px;
        }

        .supplier-count {
            background-color: #4f46e5;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .suppliers-section {
            margin-top: 16px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
        }

        .supplier-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        .supplier-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .supplier-header input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .supplier-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .supplier-fields input {
            width: 100%;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                        {{ config('app.name', 'Laravel') }}
                    </a>

                    @auth
                    <div class="hidden sm:flex space-x-6">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('products.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Products
                        </a>
                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('categories.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Categories
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('suppliers.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Suppliers
                        </a>
                    </div>
                    @endauth
                </div>

                <div class="flex items-center">
                    @auth
                    <span class="text-sm text-gray-600 mr-4 hidden sm:inline">{{ Auth::user()->name }} ({{
                        Auth::user()->email }})</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-500 hover:text-gray-700 font-medium">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium mr-4">Login</a>
                    <a href="{{ route('register') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Register</a>
                    @endauth
                </div>
            </div>
        </div>

        @auth
        <div class="sm:hidden border-t border-gray-200 px-4 py-2 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block py-2 text-sm {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : 'text-gray-600' }}">Dashboard</a>
            <a href="{{ route('products.index') }}"
                class="block py-2 text-sm {{ request()->routeIs('products.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600' }}">Products</a>
            <a href="{{ route('categories.index') }}"
                class="block py-2 text-sm {{ request()->routeIs('categories.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600' }}">Categories</a>
            <a href="{{ route('suppliers.index') }}"
                class="block py-2 text-sm {{ request()->routeIs('suppliers.*') ? 'text-indigo-600 font-semibold' : 'text-gray-600' }}">Suppliers</a>
        </div>
        @endauth
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
        <div class="flash-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="flash-error">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="py-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>