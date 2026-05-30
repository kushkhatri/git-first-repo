@extends('layouts.admin')
@section('heading', 'Store settings')
@section('content')
<form method="post" action="{{ route('admin.settings.update') }}" class="max-w-lg space-y-4 bg-white p-6 rounded-lg border">
    @csrf @method('PUT')
    <div><label class="block text-sm mb-1">Site name</label><input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Jamsora') }}" required class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Tagline</label><input name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}" class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Contact email</label><input name="contact_email" type="email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Contact phone</label><input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="w-full rounded border-stone-300"></div>
    <div><label class="block text-sm mb-1">Footer text</label><textarea name="footer_text" class="w-full rounded border-stone-300">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea></div>
    <button class="bg-amber-800 text-white px-6 py-2 rounded-lg">Save settings</button>
</form>
@endsection
