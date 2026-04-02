<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Context 1: Kitchen Settings
        if ($user->dapur_id) {
            $dapur = Dapur::findOrFail($user->dapur_id);
            return view('settings.index', [
                'type' => 'kitchen',
                'model' => $dapur,
                'title' => 'Pengaturan Dapur',
                'subtitle' => 'Kelola informasi operasional unit dapur Anda.'
            ]);
        }

        // Context 2: Yayasan (Tenant) Settings
        if ($user->hasAnyRole(['admin_yayasan', 'superadmin'])) {
            $tenant = tenant();
            return view('settings.index', [
                'type' => 'yayasan',
                'model' => $tenant,
                'title' => 'Pengaturan Yayasan',
                'subtitle' => 'Kelola identitas dan konfigurasi global Yayasan.'
            ]);
        }

        abort(403, 'Anda tidak memiliki akses ke pengaturan sistem.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->dapur_id) {
            $dapur = Dapur::findOrFail($user->dapur_id);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'province' => 'nullable|string',
                'capacity_portions' => 'nullable|integer',
            ]);
            $dapur->update($validated);
            return back()->with('status', 'settings-updated');
        }

        if ($user->hasAnyRole(['admin_yayasan', 'superadmin'])) {
            $tenant = tenant();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);
            
            $tenant->update($validated);
            return back()->with('status', 'settings-updated');
        }

        abort(403);
    }
}
