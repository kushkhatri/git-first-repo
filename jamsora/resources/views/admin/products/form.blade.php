@extends('layouts.admin')
@section('heading', $product->exists ? 'Edit product' : 'New product')
@section('content')
<form method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="max-w-2xl space-y-4 bg-white p-6 rounded-lg border">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div><label class="block text-sm mb-1">Name</label><input name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded border-stone-300"></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">SKU</label><input name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full rounded border-stone-300"></div>
        <div><label class="block text-sm mb-1">Category</label>
            <select name="category_id" class="w-full rounded border-stone-300">
                <option value="">—</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id)==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">Price ($)</label><input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" required class="w-full rounded border-stone-300"></div>
        <div><label class="block text-sm mb-1">Sale price ($)</label><input name="sale_price" type="number" step="0.01" value="{{ old('sale_price', $product->sale_price) }}" class="w-full rounded border-stone-300"></div>
    </div>
    <div><label class="block text-sm mb-1">Stock qty</label><input name="stock_qty" type="number" value="{{ old('stock_qty', $product->stock_qty ?? 1) }}" class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Image URL</label><input name="image_url" value="{{ old('image_url', $product->images->first()?->path) }}" class="w-full rounded border-stone-300" placeholder="https://..."></div>
    <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-sm mb-1">Shape</label><input name="gem_shape" value="{{ old('gem_shape', $product->gem_attributes['shape'] ?? '') }}" class="w-full rounded border-stone-300"></div>
        <div><label class="block text-sm mb-1">Carat</label><input name="gem_carat" value="{{ old('gem_carat', $product->gem_attributes['carat'] ?? '') }}" class="w-full rounded border-stone-300"></div>
        <div><label class="block text-sm mb-1">Certificate</label><input name="gem_certificate" value="{{ old('gem_certificate', $product->gem_attributes['certificate'] ?? '') }}" class="w-full rounded border-stone-300"></div>
    </div>
    <div><label class="block text-sm mb-1">Short description</label><textarea name="short_description" rows="2" class="w-full rounded border-stone-300">{{ old('short_description', $product->short_description) }}</textarea></div>
    <div><label class="block text-sm mb-1">Description</label><textarea name="description" rows="4" class="w-full rounded border-stone-300">{{ old('description', $product->description) }}</textarea></div>
    <div class="flex gap-4">
        <label><input type="checkbox" name="manage_stock" value="1" @checked(old('manage_stock', $product->manage_stock ?? true))> Manage stock</label>
        <label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Featured</label>
    </div>
    <div><label class="block text-sm mb-1">Status</label>
        <select name="status" class="w-full rounded border-stone-300">
            <option value="published" @selected(old('status', $product->status)==='published')>Published</option>
            <option value="draft" @selected(old('status', $product->status)==='draft')>Draft</option>
        </select>
    </div>
    <button class="bg-amber-800 text-white px-6 py-2 rounded-lg">Save</button>
</form>
@endsection
