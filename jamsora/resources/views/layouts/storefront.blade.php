<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Jamsora') — Fine Gemstones</title>
    <meta name="description" content="@yield('meta_description', 'Curated fine gemstones — sapphires, emeralds, and certified gems direct from Jamsora.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col" x-data="{ mobileOpen: false }">
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-jamsora-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[4.5rem]">
                <x-jamsora-logo variant="dark" />
                <nav class="hidden md:flex items-center gap-10 text-xs font-medium uppercase tracking-[0.2em] text-jamsora-muted">
                    <a href="{{ route('shop.index') }}" class="hover:text-jamsora-ink transition">Shop</a>
                    <a href="{{ route('pages.show', 'price-match-guarantee') }}" class="hover:text-jamsora-ink transition">Price Match</a>
                    <a href="{{ route('pages.show', 'about-us') }}" class="hover:text-jamsora-ink transition">About</a>
                </nav>
                <div class="flex items-center gap-5">
                    <form action="{{ route('shop.index') }}" method="get" class="hidden lg:block">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search"
                               class="input-field w-44 rounded-full py-2 px-4">
                    </form>
                    <a href="{{ route('cart.index') }}" class="relative text-jamsora-ink hover:text-jamsora-champagne transition" aria-label="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-jamsora-ink text-white text-[10px] font-medium rounded-full min-w-[1.125rem] h-[1.125rem] flex items-center justify-center px-0.5">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:block text-xs uppercase tracking-widest text-jamsora-champagne font-medium">Admin</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="text-xs uppercase tracking-widest text-jamsora-muted hover:text-jamsora-ink">Account</a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs uppercase tracking-widest text-jamsora-muted hover:text-jamsora-ink">Sign in</a>
                    @endauth
                    <button type="button" class="md:hidden text-jamsora-ink" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
            <nav x-show="mobileOpen" x-cloak class="md:hidden pb-4 flex flex-col gap-3 text-xs uppercase tracking-[0.2em] text-jamsora-muted border-t border-jamsora-border pt-4">
                <a href="{{ route('shop.index') }}">Shop</a>
                <a href="{{ route('pages.show', 'price-match-guarantee') }}">Price Match</a>
                <a href="{{ route('pages.show', 'about-us') }}">About</a>
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-jamsora-ink text-white text-center py-2.5 text-xs uppercase tracking-widest">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-900 text-white text-center py-2.5 text-xs uppercase tracking-widest">{{ session('error') }}</div>
    @endif

    <main class="flex-1">@yield('content')</main>

    <footer class="bg-jamsora-charcoal text-jamsora-subtle mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-16 grid md:grid-cols-4 gap-10 text-sm">
            <div class="md:col-span-2">
                <x-jamsora-logo variant="light" class="mb-4" />
                <p class="text-jamsora-subtle max-w-sm leading-relaxed">Direct-to-customer fine gemstones with certification and transparent pricing.</p>
            </div>
            <div>
                <p class="text-white text-xs uppercase tracking-[0.2em] mb-4">Shop</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">All gemstones</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'sapphire']) }}" class="hover:text-white transition">Sapphire</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'emerald']) }}" class="hover:text-white transition">Emerald</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white text-xs uppercase tracking-[0.2em] mb-4">Support</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('pages.show', 'price-match-guarantee') }}" class="hover:text-white transition">Price Match</a></li>
                    <li><a href="{{ route('pages.show', 'about-us') }}" class="hover:text-white transition">About us</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 text-center py-6 text-xs text-jamsora-subtle tracking-wider">
            &copy; {{ date('Y') }} Jamsora. All rights reserved.
        </div>
    </footer>
</body>
</html>
