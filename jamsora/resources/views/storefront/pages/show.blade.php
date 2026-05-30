@extends('layouts.storefront')
@section('title', $page->title)
@section('content')
<article class="max-w-3xl mx-auto px-4 py-12 prose prose-stone max-w-none">
    <h1 class="font-serif">{{ $page->title }}</h1>
    {!! $page->body !!}
</article>
@endsection
