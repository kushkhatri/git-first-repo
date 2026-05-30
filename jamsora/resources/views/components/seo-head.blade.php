@props(['meta' => [], 'schemas' => []])

@if(!empty($meta['canonical']))
    @push('head')
        <link rel="canonical" href="{{ $meta['canonical'] }}">
    @endpush
@endif
@if(!empty($meta['og_image']))
    @push('head')
        <meta property="og:image" content="{{ $meta['og_image'] }}">
    @endpush
@endif
@foreach($schemas as $schema)
    @push('head')
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
@endforeach
