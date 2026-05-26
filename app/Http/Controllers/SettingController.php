<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $general = Setting::getGroup('general');
        $system  = Setting::getGroup('system');
        $stock   = Setting::getGroup('stock');

        return view('settings.index', compact('general', 'system', 'stock'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name'            => 'required|string|max:100',
            'company_address'         => 'nullable|string|max:255',
            'company_phone'           => 'nullable|string|max:20',
            'company_email'           => 'nullable|email|max:100',
            'company_logo'            => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'currency'                => 'required|string|max:20',
            'currency_symbol'         => 'required|string|max:5',
            'date_format'             => 'required|string|max:20',
            'items_per_page'          => 'required|integer|min:5|max:100',
            'timezone'                => 'required|string|max:50',
            'default_min_stock_alert' => 'required|integer|min:0',
        ]);

        $keys = [
            'company_name', 'company_address', 'company_phone', 'company_email',
            'currency', 'currency_symbol', 'date_format', 'items_per_page', 'timezone',
            'default_min_stock_alert',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        if ($request->hasFile('company_logo')) {
            $old = Setting::get('company_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('company_logo')->store('settings', 'public');
            Setting::set('company_logo', $path);
        }

        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            $old = Setting::get('company_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('company_logo', '');
        }

        Setting::set('low_stock_notify', $request->has('low_stock_notify') ? '1' : '0');
        Setting::set('out_stock_notify', $request->has('out_stock_notify') ? '1' : '0');

        AuditLog::log('settings_update', 'ແກ້ໄຂຕັ້ງຄ່າລະບົບ');

        return redirect()->route('settings.index')->with('success', 'ບັນທຶກການຕັ້ງຄ່າສຳເລັດ');
    }
}
