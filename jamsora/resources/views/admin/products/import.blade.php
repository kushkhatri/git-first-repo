@extends('layouts.admin')
@section('heading', 'Import products from CSV')

@section('content')
<div class="max-w-2xl">
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <p class="text-sm text-stone-600 mb-6">
        Upload a WooCommerce or Google Sheets CSV export. Products are assigned to categories using the
        <strong>Categories</strong> column (and gemstone attributes when present). Maximum file size:
        <strong>{{ $maxMb }} MB</strong>.
    </p>

    <form action="{{ route('admin.products.import.store') }}" method="post" enctype="multipart/form-data" class="bg-white border border-stone-200 rounded-lg p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-2">CSV file</label>
            <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                   class="block w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-900 hover:file:bg-amber-100">
            @error('csv_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-start gap-3 text-sm">
            <input type="checkbox" name="fresh" value="1" class="mt-1 rounded border-stone-300 text-amber-800 focus:ring-amber-700">
            <span>
                <strong>Replace all products</strong> — delete existing catalog before import (cannot be undone).
            </span>
        </label>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="bg-amber-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-900">
                Upload &amp; import
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-lg text-sm border border-stone-300 text-stone-700 hover:bg-stone-50">
                Cancel
            </a>
        </div>
    </form>

    <div class="mt-8 text-xs text-stone-500 space-y-2">
        <p>Supported formats: WooCommerce product export, or custom CSV matching <code>database/data/products.csv.example</code>.</p>
        <p>Large files (10 MB+) require PHP <code>upload_max_filesize</code> and <code>post_max_size</code> of at least 128M on the server.</p>
    </div>
</div>
@endsection
