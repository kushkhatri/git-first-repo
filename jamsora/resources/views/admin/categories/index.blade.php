@extends('layouts.admin')
@section('heading', 'Categories')
@section('content')
<a href="{{ route('admin.categories.create') }}" class="inline-block mb-6 bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Add category</a>
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50"><tr><th class="p-3 text-left">Name</th><th class="p-3">Products</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
        <tbody>
            @foreach($categories as $category)
                <tr class="border-t">
                    <td class="p-3">{{ $category->name }}</td>
                    <td class="p-3">{{ $category->products_count }}</td>
                    <td class="p-3">{{ $category->status }}</td>
                    <td class="p-3 text-right"><a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-800">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $categories->links() }}
@endsection
