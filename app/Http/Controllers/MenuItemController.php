<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu-items.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menu-items.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('boms.material');

        return view('menu-items.show', compact('menuItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem)
    {
        return view('menu-items.edit', compact('menuItem'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
