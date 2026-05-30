@extends('layouts.admin')
@section('heading', $certification->exists ? 'Edit certification' : 'New certification')
@section('content')
<form method="post" action="{{ $certification->exists ? route('admin.certifications.update', $certification) : route('admin.certifications.store') }}" class="max-w-xl space-y-4 bg-white p-6 rounded-lg border">
    @csrf
    @if($certification->exists) @method('PUT') @endif
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input name="name" value="{{ old('name', $certification->name) }}" required class="w-full rounded border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Agency</label>
        <input name="agency" value="{{ old('agency', $certification->agency) }}" class="w-full rounded border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Certificate number</label>
        <input name="certificate_number" value="{{ old('certificate_number', $certification->certificate_number) }}" class="w-full rounded border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Verification info</label>
        <textarea name="verification_info" rows="3" class="w-full rounded border-stone-300">{{ old('verification_info', $certification->verification_info) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Image URL</label>
        <input name="image_path" value="{{ old('image_path', $certification->image_path) }}" class="w-full rounded border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">PDF URL</label>
        <input name="pdf_path" value="{{ old('pdf_path', $certification->pdf_path) }}" class="w-full rounded border-stone-300">
    </div>
    <button class="bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Save</button>
</form>
@endsection
