<?php

namespace App\Http\Controllers;

use App\Models\MenuPeriod;
use Illuminate\Http\Request;

class MenuPeriodController extends Controller
{
    public function index()
    {
        return view('menu-periods.index');
    }

    public function create()
    {
        return view('menu-periods.create');
    }

    public function show(MenuPeriod $menuPeriod)
    {
        return view('menu-periods.show', compact('menuPeriod'));
    }

    public function edit(MenuPeriod $menuPeriod)
    {
        return view('menu-periods.edit', compact('menuPeriod'));
    }

    /**
     * Ajukan menu periode untuk review Kepala Dapur.
     */
    public function submit(MenuPeriod $menuPeriod)
    {
        if ($menuPeriod->status !== MenuPeriod::STATUS_DRAF && $menuPeriod->status !== MenuPeriod::STATUS_REJECTED) {
            return back()->with('error', 'Status menu tidak valid untuk diajukan.');
        }

        $menuPeriod->update(['status' => MenuPeriod::STATUS_PENDING]);

        return back()->with('success', 'Rancangan menu berhasil diajukan untuk review.');
    }

    /**
     * Setujui menu periode oleh Kepala Dapur.
     */
    public function approve(MenuPeriod $menuPeriod)
    {
        if ($menuPeriod->status !== MenuPeriod::STATUS_PENDING) {
            return back()->with('error', 'Hanya menu dengan status menunggu yang bisa disetujui.');
        }

        $menuPeriod->update([
            'status' => MenuPeriod::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Menu periode berhasil disetujui dan dikunci.');
    }

    /**
     * Tolak menu periode oleh Kepala Dapur.
     */
    public function reject(Request $request, MenuPeriod $menuPeriod)
    {
        $request->validate(['rejection_note' => 'required|string|max:500']);

        if ($menuPeriod->status !== MenuPeriod::STATUS_PENDING) {
            return back()->with('error', 'Hanya menu dengan status menunggu yang bisa ditolak.');
        }

        $menuPeriod->update([
            'status' => MenuPeriod::STATUS_REJECTED,
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', 'Menu periode telah ditolak untuk dikoreksi.');
    }
}
