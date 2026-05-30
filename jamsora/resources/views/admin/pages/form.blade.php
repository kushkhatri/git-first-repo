@extends('layouts.admin')
@section('heading', $page->exists ? 'Edit page' : 'New page')
@section('content')
<form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="max-w-3xl space-y-4 bg-white p-6 rounded-lg border">
    @csrf @if($page->exists) @method('PUT') @endif
    <div><label class="block text-sm mb-1">Title</label><input name="title" value="{{ old('title', $page->title) }}" required class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Body (HTML allowed)</label><textarea name="body" rows="12" class="w-full rounded border-stone-300 font-mono text-sm">{{ old('body', $page->body) }}</textarea></div>
    <div><label class="block text-sm mb-1">Status</label>
        <select name="status" class="w-full rounded border-stone-300">
            <option value="published" @selected(old('status', $page->status)==='published')>Published</option>
            <option value="draft" @selected(old('status', $page->status)==='draft')>Draft</option>
        </select>
    </div>
    <button class="bg-amber-800 text-white px-6 py-2 rounded-lg">Save</button>
</form>
@endsection
