@extends('layouts.admin')
@section('heading', 'Certifications')
@section('content')
<div class="flex justify-between mb-6">
    <p class="text-sm text-stone-600">Manage lab certificates assigned to products.</p>
    <a href="{{ route('admin.certifications.create') }}" class="bg-amber-800 text-white px-4 py-2 rounded-lg text-sm">Add certification</a>
</div>
<table class="w-full bg-white rounded-lg border text-sm">
    <thead class="border-b text-left">
        <tr><th class="p-3">Name</th><th class="p-3">Agency</th><th class="p-3">Number</th><th class="p-3"></th></tr>
    </thead>
    <tbody>
        @foreach($certifications as $cert)
            <tr class="border-b">
                <td class="p-3">{{ $cert->name }}</td>
                <td class="p-3">{{ $cert->agency }}</td>
                <td class="p-3">{{ $cert->certificate_number }}</td>
                <td class="p-3 text-right space-x-2">
                    <a href="{{ route('admin.certifications.edit', $cert) }}" class="text-amber-800">Edit</a>
                    <form action="{{ route('admin.certifications.destroy', $cert) }}" method="post" class="inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-red-700">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $certifications->links() }}
@endsection
