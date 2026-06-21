<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => Setting::get('app_name', 'Sistem Informasi Nilai Murid'),
            'footer_text' => Setting::get('footer_text', '© 2026 SINM. All Rights Reserved.'),
            'school_name' => Setting::get('school_name'),
            'school_address' => Setting::get('school_address'),
            'school_phone' => Setting::get('school_phone'),
            'school_website' => Setting::get('school_website'),
            'headmaster_name' => Setting::get('headmaster_name'),
            'headmaster_nip' => Setting::get('headmaster_nip'),
        ];

        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:255',
            'footer_text' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_phone' => 'nullable|string|max:100',
            'school_website' => 'nullable|string|max:100',
            'headmaster_name' => 'required|string|max:255',
            'headmaster_nip' => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.setting.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
