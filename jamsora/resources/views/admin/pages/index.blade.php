@extends('layouts.admin')
@section('heading', 'CMS Pages')
@section('content')
<a href="{{ route('admin.pages.create') }}" class="inline-block mb-6 bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Add page</a>
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50"><tr><th class="p-3 text-left">Title</th><th class="p-3">Slug</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
        <tbody>
            @foreach($pages as $page)
                <tr class="border-t">
                    <td class="p-3">{{ $page->title }}</td>
                    <td class="p-3 text-stone-500">{{ $page->slug }}</td>
                    <td class="p-3">{{ $page->status }}</td>
                    <td class="p-3 text-right"><a href="{{ route('admin.pages.edit', $page) }}" class="text-amber-800">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $pages->links() }}
@endsection
