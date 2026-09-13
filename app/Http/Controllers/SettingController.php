<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('pages.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
        ]);

        $keys = ['company_name', 'company_address', 'company_phone', 'receipt_footer'];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $validated[$key]]
            );
        }

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'company_logo'],
                ['value' => '/storage/' . $path]
            );
        }

        if ($request->hasFile('company_favicon')) {
            $path = $request->file('company_favicon')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'company_favicon'],
                ['value' => '/storage/' . $path]
            );
        }

        return back()->with('success', 'Pengaturan toko berhasil disimpan!');
    }
}
