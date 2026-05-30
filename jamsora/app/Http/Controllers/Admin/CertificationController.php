<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    public function index()
    {
        $certifications = Certification::query()->latest()->paginate(20);

        return view('admin.certifications.index', compact('certifications'));
    }

    public function create()
    {
        return view('admin.certifications.form', ['certification' => new Certification]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Certification::create($data);

        return redirect()->route('admin.certifications.index')->with('success', 'Certification created.');
    }

    public function edit(Certification $certification)
    {
        return view('admin.certifications.form', compact('certification'));
    }

    public function update(Request $request, Certification $certification)
    {
        $certification->update($this->validated($request));

        return redirect()->route('admin.certifications.index')->with('success', 'Certification updated.');
    }

    public function destroy(Certification $certification)
    {
        $certification->delete();

        return redirect()->route('admin.certifications.index')->with('success', 'Certification removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'agency' => ['nullable', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'verification_info' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:2048'],
            'pdf_path' => ['nullable', 'string', 'max:2048'],
        ]);
    }
}
