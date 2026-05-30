@props(['variant' => 'dark', 'class' => ''])

@php
    $custom = $variant === 'light'
        ? (file_exists(public_path('brand/logo-light.png')) ? asset('brand/logo-light.png') : (file_exists(public_path('brand/logo-light.svg')) ? asset('brand/logo-light.svg') : null))
        : (file_exists(public_path('brand/logo-dark.png')) ? asset('brand/logo-dark.png') : (file_exists(public_path('brand/logo.png')) ? asset('brand/logo.png') : null));
    $src = $custom ?? ($variant === 'light' ? asset('brand/jamsora-logo-light.svg') : asset('brand/jamsora-logo-dark.svg'));
@endphp

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center '.$class]) }}>
    <img src="{{ $src }}" alt="Jamsora" class="h-7 md:h-8 w-auto object-contain" width="180" height="32">
</a>
