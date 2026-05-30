@extends('layouts.storefront')
@section('title', $page->title)

@section('content')
<section class="bg-jamsora-cream border-b border-jamsora-border py-14">
    <div class="max-w-site mx-auto px-4 text-center">
        <h1 class="font-display text-4xl md:text-5xl text-jamsora-ink">{{ $page->title }}</h1>
    </div>
</section>
<article class="max-w-3xl mx-auto px-4 py-12 md:py-16 prose prose-stone prose-headings:font-display prose-a:text-jamsora-gold">
    {!! $page->body !!}
</article>
@endsection
