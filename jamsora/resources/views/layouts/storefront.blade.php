<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Jamsora') — Fine Gemstones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-900 font-sans antialiased" x-data="{ mobileMenu: false }">
    <header class="border-b border-stone-200 bg-white/95 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="font-serif text-2xl tracking-wide text-stone-900">Jamsora</a>
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-stone-600">
                    <a href="{{ route('shop.index') }}" class="hover:text-amber-800">Shop</a>
                    <a href="{{ route('pages.show', 'price-match-guarantee') }}" class="hover:text-amber-800">Price Match</a>
                    <a href="{{ route('pages.show', 'about-us') }}" class="hover:text-amber-800">About</a>
                </nav>
                <div class="flex items-center gap-4">
                    <form action="{{ route('shop.index') }}" method="get" class="hidden lg:block">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search gemstones…"
                               class="rounded-full border-stone-300 text-sm w-48 focus:border-amber-700 focus:ring-amber-700">
                    </form>
                    <a href="{{ route('cart.index') }}" class="relative text-stone-700 hover:text-amber-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-amber-800 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-amber-800 font-medium">Admin</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="text-sm text-stone-600">Account</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-stone-600">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 text-center py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-800 text-center py-3 text-sm">{{ session('error') }}</div>
    @endif

    <main>@yield('content')</main>

    <footer class="bg-stone-900 text-stone-300 mt-20">
        <div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-3 gap-8 text-sm">
            <div>
                <p class="font-serif text-xl text-white mb-2">Jamsora</p>
                <p class="text-stone-400">Direct-to-customer fine gemstones & jewelry.</p>
            </div>
            <div>
                <p class="text-white font-medium mb-2">Shop</p>
                <ul class="space-y-1 text-stone-400">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white">All gemstones</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'sapphire']) }}" class="hover:text-white">Sapphire</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'emerald']) }}" class="hover:text-white">Emerald</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-medium mb-2">Policies</p>
                <ul class="space-y-1 text-stone-400">
                    <li><a href="{{ route('pages.show', 'price-match-guarantee') }}" class="hover:text-white">Price Match Guarantee</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-stone-800 text-center py-6 text-xs text-stone-500">
            &copy; {{ date('Y') }} Jamsora. Built with Laravel CMS.
        </div>
    </footer>
</body>
</html>
