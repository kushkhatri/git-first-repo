<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(SettingService $settings)
    {
        return view('admin.settings.index', [
            'settings' => $settings->all('store'),
        ]);
    }

    public function update(Request $request, SettingService $settings)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'footer_text' => ['nullable', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            $settings->set($key, $value, 'store');
        }

        return back()->with('success', 'Settings saved.');
    }
}
