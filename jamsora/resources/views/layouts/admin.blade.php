<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Jamsora CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-stone-900 text-stone-300 flex-shrink-0">
            <div class="p-6 border-b border-stone-800">
                <a href="{{ route('admin.dashboard') }}" class="font-serif text-xl text-white">Jamsora CMS</a>
            </div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.dashboard') ? 'bg-stone-800 text-white' : '' }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.products.*') ? 'bg-stone-800 text-white' : '' }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.categories.*') ? 'bg-stone-800 text-white' : '' }}">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.orders.*') ? 'bg-stone-800 text-white' : '' }}">Orders</a>
                <a href="{{ route('admin.pages.index') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.pages.*') ? 'bg-stone-800 text-white' : '' }}">Pages</a>
                <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded hover:bg-stone-800 {{ request()->routeIs('admin.settings.*') ? 'bg-stone-800 text-white' : '' }}">Settings</a>
                <hr class="border-stone-800 my-4">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded hover:bg-stone-800">View storefront</a>
            </nav>
        </aside>
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-stone-200 px-8 py-4 flex justify-between items-center">
                <h1 class="text-lg font-semibold text-stone-800">@yield('heading', 'Admin')</h1>
                <span class="text-sm text-stone-500">{{ auth()->user()->name }}</span>
            </header>
            <main class="p-8 flex-1">
                @if(session('success'))
                    <div class="mb-4 rounded bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
