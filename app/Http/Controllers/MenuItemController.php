<?php

namespace App\Http\Controllers;

use App\Imports\MenuItemsImport;
use App\Models\Dapur;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dapurs = Dapur::orderBy('name')->get();

        return view('menu-items.index', compact('dapurs'));
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
     * Import menu items from excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'dapur_id' => 'nullable|exists:dapurs,id',
        ]);

        try {
            Excel::import(new MenuItemsImport($request->dapur_id), $request->file('file'));

            return redirect()->route('menu-items.index')->with('success', 'Data menu berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('menu-items.index')->with('error', 'Terjadi kesalahan saat mengimport data: '.$e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_menu.csv"',
        ];

        $columns = ['nama', 'tipe_makan', 'porsi', 'keterangan', 'kalori', 'protein', 'karbo', 'lemak', 'serat', 'komposisi_bahan'];

        return response()->stream(function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Tambahkan baris instruksi
            fputcsv($file, [
                '--- PETUNJUK PENGISIAN ---',
                'Format komposisi_bahan -> KODE:JUMLAH|KODE:JUMLAH',
                'Contoh: BB-BER-01:0.1',
                'Pemisah antar bahan gunakan tanda |',
                '', '', '', '', '', '',
            ]);
            // Baris contoh data nyata
            fputcsv($file, ['Nasi Putih', 'siang', '1', 'Nasi putih pulen', '130', '2.7', '28', '0.3', '0.4', 'BB-BER-01:0.1']);
            fputcsv($file, ['Telur Dadar', 'pagi', '1', 'Telur dadar goreng', '150', '10', '1', '11', '0', 'BB-TLR-01:1|BB-MYK-01:0.01']);
            fclose($file);
        }, 200, $headers);
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
