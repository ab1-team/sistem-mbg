<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;
use App\Imports\MaterialsImport;
use App\Models\Dapur;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
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
            return redirect()->route('materials.index')->with('error', 'Terjadi kesalahan saat mengimport data: '.$e->getMessage());
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

        $columns = ['kode', 'nama', 'kategori', 'satuan', 'kalori', 'protein', 'karbo', 'lemak', 'serat', 'estimasi_harga', 'min_stok', 'supplier_1', 'supplier_2', 'supplier_3'];

        return response()->stream(function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Tambahkan baris instruksi
            fputcsv($file, [
                '--- PETUNJUK PENGISIAN ---',
                'Kolom nutrisi opsional. Kategori: sayuran,daging,ikan,bumbu,sembako,minuman,lainnya',
                'Supplier: Isi dengan KODE supplier (otomatis terdaftar jika belum ada)',
                '', '', '', '', '', '', '', '', '', '', '',
            ]);
            // Add example row
            fputcsv($file, ['BB-BER-01', 'Beras Ramos', 'sembako', 'Kg', '365', '7', '80', '0.6', '1.3', '15000', '50', 'SUP001', 'SUP002', '']);
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $categories = ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'];
        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();

        return view('materials.create', compact('categories', 'dapurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($user->dapur_id) {
            $validated['dapur_id'] = $user->dapur_id;
        }

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
        $user = auth()->user();

        // Cek akses: Kepala dapur hanya bisa edit bahan di dapurnya sendiri atau bahan global
        if ($user->dapur_id && $material->dapur_id && $material->dapur_id !== $user->dapur_id) {
            return redirect()->route('materials.index')->with('error', 'Anda tidak memiliki akses ke bahan ini.');
        }

        $categories = ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'];
        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('materials.edit', compact('material', 'categories', 'dapurs', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialRequest $request, Material $material)
    {
        $user = auth()->user();

        // Cek akses sebelum update
        if ($user->dapur_id && $material->dapur_id && $material->dapur_id !== $user->dapur_id) {
            abort(403);
        }

        $validated = $request->validated();

        if ($user->dapur_id) {
            $validated['dapur_id'] = $user->dapur_id;
        }

        $material->update($validated);

        if ($request->has('suppliers')) {
            $material->suppliers()->sync($request->input('suppliers'));
        } else {
            $material->suppliers()->sync([]);
        }

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
