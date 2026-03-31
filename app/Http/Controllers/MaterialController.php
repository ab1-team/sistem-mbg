<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Imports\MaterialsImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::latest()->paginate(10);
        return view('materials.index', compact('materials'));
    }

    /**
     * Import materials from excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new MaterialsImport, $request->file('file'));
            return redirect()->route('materials.index')->with('success', 'Data bahan baku berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('materials.index')->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_bahan_baku.csv"',
        ];

        $columns = ['kode', 'nama', 'kategori', 'satuan', 'kalori', 'protein', 'karbo', 'lemak', 'serat', 'estimasi_harga', 'min_stok'];

        return response()->stream(function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add example row
            fputcsv($file, ['BB-BER-01', 'Beras Ramos', 'sembako', 'Kg', '365', '7', '80', '0.6', '1.3', '15000', '50']);
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'];
        return view('materials.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:materials',
            'name' => 'required|string|max:150',
            'category' => ['required', Rule::in(['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'])],
            'unit' => 'required|string|max:20',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'price_estimate' => 'required|numeric|min:0',
            'min_stock_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $categories = ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'];
        return view('materials.edit', compact('material', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('materials')->ignore($material->id)],
            'name' => 'required|string|max:150',
            'category' => ['required', Rule::in(['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'])],
            'unit' => 'required|string|max:20',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'price_estimate' => 'required|numeric|min:0',
            'min_stock_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
