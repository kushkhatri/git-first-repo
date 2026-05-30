<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Jamsora CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-jamsora-cream min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-jamsora-ink text-jamsora-subtle flex-shrink-0 flex flex-col">
            <div class="p-6 border-b border-white/10">
                <x-jamsora-logo variant="light" />
                <p class="text-[10px] uppercase tracking-[0.3em] mt-3 text-jamsora-subtle">CMS</p>
            </div>
            <nav class="p-4 space-y-0.5 text-xs uppercase tracking-[0.15em] flex-1">
                @foreach([
                    ['admin.dashboard', 'Dashboard'],
                    ['admin.products.index', 'Products'],
                    ['admin.categories.index', 'Categories'],
                    ['admin.orders.index', 'Orders'],
                    ['admin.pages.index', 'Pages'],
                    ['admin.settings.index', 'Settings'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="block px-3 py-2.5 rounded-sm transition {{ request()->routeIs(str_replace('.index', '.*', $route).'*') || request()->routeIs($route) ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="p-4 border-t border-white/10">
                <a href="{{ route('home') }}" class="text-[10px] uppercase tracking-[0.2em] hover:text-white transition">← Storefront</a>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-jamsora-border px-8 py-4 flex justify-between items-center">
                <h1 class="text-sm font-medium uppercase tracking-[0.15em] text-jamsora-ink">@yield('heading', 'Admin')</h1>
                <span class="text-xs text-jamsora-muted">{{ auth()->user()->name }}</span>
            </header>
            <main class="p-8 flex-1">
                @if(session('success'))
                    <div class="mb-6 border border-jamsora-ink bg-jamsora-ink text-white px-4 py-3 text-xs uppercase tracking-widest">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
