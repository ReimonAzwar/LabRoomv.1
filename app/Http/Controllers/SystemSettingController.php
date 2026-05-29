<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class SystemSettingController extends Controller
{
    /**
     * Get all system settings.
     */
    public function index(): JsonResponse
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return response()->json(['success' => true, 'settings' => $settings]);
    }

    /**
     * Update/Save system settings.
     * Accessible only by Super Admin.
     */
    public function store(Request $request): JsonResponse
    {
        if (auth('web')->check() && auth('web')->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $settings = $request->validate([
            'system_name' => 'required|string|max:255',
            'whatsapp_notification' => 'required|in:true,false',
            'operational_hours_start' => 'required|string|regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            'operational_hours_end' => 'required|string|regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            'contact_phone' => 'sometimes|nullable|string|max:50',
            'contact_email' => 'sometimes|nullable|string|max:255',
            'contact_address' => 'sometimes|nullable|string',
            'lab_capacity_info' => 'sometimes|nullable|string',
            'gmaps_iframe_url' => 'sometimes|nullable|string'
        ]);

        foreach ($settings as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Generate activity log
        ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => 'update_settings',
            'details' => "Admin " . (auth('web')->user()->name ?? 'System') . " memperbarui pengaturan sistem (Jam Operasional: {$settings['operational_hours_start']}–{$settings['operational_hours_end']}).",
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true, 'message' => 'Pengaturan sistem berhasil diperbarui.']);
    }
}
