<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Jamsora') — Fine Gemstones & Jewelry</title>
    <meta name="description" content="@yield('meta_description', 'Curated fine gemstones — certified sapphires, emeralds, and rare gems from Jamsora.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col text-jamsora-ink" x-data="{ mobileOpen: false, searchOpen: false }">
    {{-- Top promo bar --}}
    <div class="bg-jamsora-cream border-b border-jamsora-border text-center py-2.5 text-[11px] uppercase tracking-[0.25em] text-jamsora-muted">
        Free insured shipping on orders over $500 · <a href="{{ route('pages.show', 'price-match-guarantee') }}" class="text-jamsora-gold hover:underline">Price match guarantee</a>
    </div>

    <header class="sticky top-0 z-50 bg-white border-b border-jamsora-border shadow-sm shadow-black/[0.03]">
        <div class="max-w-site mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20 lg:h-24">
                <button type="button" class="lg:hidden p-2 -ml-2" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <nav class="hidden lg:flex items-center gap-8 text-[13px] uppercase tracking-[0.15em] text-jamsora-charcoal flex-1">
                    <a href="{{ route('home') }}" class="hover:text-jamsora-gold transition {{ request()->routeIs('home') ? 'text-jamsora-gold' : '' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-jamsora-gold transition {{ request()->routeIs('shop.*', 'products.*') ? 'text-jamsora-gold' : '' }}">Shop</a>
                    @foreach(($headerCategories ?? collect())->take(4) as $cat)
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="hover:text-jamsora-gold transition">{{ $cat->name }}</a>
                    @endforeach
                </nav>

                <div class="flex-shrink-0 px-4 lg:px-10">
                    <x-jamsora-logo variant="dark" class="mx-auto" />
                </div>

                <div class="flex items-center justify-end gap-4 lg:gap-6 flex-1">
                    <button type="button" @click="searchOpen = !searchOpen" class="hidden sm:block p-1 hover:text-jamsora-gold transition" aria-label="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-5.2-5.2M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    </button>
                    <a href="{{ route('cart.index') }}" class="relative p-1 hover:text-jamsora-gold transition" aria-label="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 bg-jamsora-gold text-white text-[9px] font-bold rounded-full min-w-[1rem] h-4 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:block text-[11px] uppercase tracking-widest text-jamsora-gold">Admin</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="hidden md:block text-[11px] uppercase tracking-widest hover:text-jamsora-gold">Account</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:block text-[11px] uppercase tracking-widest hover:text-jamsora-gold">Login</a>
                    @endauth
                </div>
            </div>

            <div x-show="searchOpen" x-cloak x-transition class="pb-4 border-t border-jamsora-border pt-4">
                <form action="{{ route('shop.index') }}" method="get" class="flex max-w-xl mx-auto gap-2">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search gemstones…" class="input-dagas flex-1">
                    <button type="submit" class="btn-dagas py-2 px-6">Search</button>
                </form>
            </div>

            <nav x-show="mobileOpen" x-cloak class="lg:hidden pb-6 flex flex-col gap-3 text-sm uppercase tracking-widest border-t border-jamsora-border pt-4">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('shop.index') }}">Shop</a>
                <a href="{{ route('pages.show', 'about-us') }}">About</a>
                <a href="{{ route('pages.show', 'price-match-guarantee') }}">Price Match</a>
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-jamsora-gold text-white text-center py-2.5 text-[11px] uppercase tracking-widest">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-800 text-white text-center py-2.5 text-[11px] uppercase tracking-widest">{{ session('error') }}</div>
    @endif

    <main class="flex-1">@yield('content')</main>

    <footer class="bg-jamsora-cream border-t border-jamsora-border mt-16">
        <div class="max-w-site mx-auto px-4 lg:px-8 py-16 grid md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="lg:col-span-1">
                <x-jamsora-logo variant="dark" class="mb-6" />
                <p class="text-sm text-jamsora-muted leading-relaxed">Certified gemstones with transparent pricing — your trusted source for sapphires, emeralds, and rare gems.</p>
            </div>
            <div>
                <h4 class="subheading-dagas mb-5">Shop</h4>
                <ul class="space-y-2.5 text-sm text-jamsora-muted">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-jamsora-gold transition">All gemstones</a></li>
                    @foreach(($headerCategories ?? collect())->take(6) as $cat)
                        <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="hover:text-jamsora-gold transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="subheading-dagas mb-5">Customer care</h4>
                <ul class="space-y-2.5 text-sm text-jamsora-muted">
                    <li><a href="{{ route('pages.show', 'price-match-guarantee') }}" class="hover:text-jamsora-gold transition">Price match</a></li>
                    <li><a href="{{ route('pages.show', 'about-us') }}" class="hover:text-jamsora-gold transition">About us</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-jamsora-gold transition">Shopping cart</a></li>
                </ul>
            </div>
            <div>
                <h4 class="subheading-dagas mb-5">Newsletter</h4>
                <p class="text-sm text-jamsora-muted mb-4">New arrivals and exclusive offers.</p>
                <form class="flex border-b border-jamsora-border" onsubmit="return false;">
                    <input type="email" placeholder="Your email" class="input-dagas flex-1 text-sm">
                    <button type="button" class="text-jamsora-gold text-xs uppercase tracking-widest px-2">Join</button>
                </form>
            </div>
        </div>
        <div class="border-t border-jamsora-border py-6 text-center text-xs text-jamsora-subtle tracking-wider">
            &copy; {{ date('Y') }} Jamsora. All rights reserved.
        </div>
    </footer>
</body>
</html>
