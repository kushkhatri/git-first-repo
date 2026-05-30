@extends('layouts.admin')
@section('heading', $category->exists ? 'Edit category' : 'New category')
@section('content')
<form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="max-w-lg space-y-4 bg-white p-6 rounded-lg border">
    @csrf @if($category->exists) @method('PUT') @endif
    <div><label class="block text-sm mb-1">Name</label><input name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Description</label><textarea name="description" class="w-full rounded border-stone-300">{{ old('description', $category->description) }}</textarea></div>
    <div><label class="block text-sm mb-1">Status</label>
        <select name="status" class="w-full rounded border-stone-300">
            <option value="active" @selected(old('status', $category->status)==='active')>Active</option>
            <option value="inactive" @selected(old('status', $category->status)==='inactive')>Inactive</option>
        </select>
    </div>
    <button class="bg-amber-800 text-white px-6 py-2 rounded-lg">Save</button>
</form>
@endsection
