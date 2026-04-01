<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periods = Period::orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(12);

        return view('periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $month = $request->month;
        $year = $request->year;

        // Cek tumpang tindih tanggal (Overlap Detection)
        $overlap = Period::where(function ($q) use ($request) {
            $q->where('start_date', '<=', $request->end_date)
                ->where('end_date', '>=', $request->start_date);
        })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'Gagal: Rentang tanggal ini tumpang tindih dengan periode lain yang sudah ada.'])->withInput();
        }

        // Generate kode unik (Auto-increment jika bulan yang sama sudah ada)
        $baseCode = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);
        $code = $baseCode;
        $existingCount = Period::where('year', $year)->where('month', $month)->count();
        if ($existingCount > 0) {
            $code = $baseCode.'-'.($existingCount + 1);
        }

        Period::create([
            'code' => $code,
            'name' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y').($existingCount > 0 ? ' ('.($existingCount + 1).')' : ''),
            'month' => $month,
            'year' => $year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'open',
        ]);

        return redirect()->route('periods.index')
            ->with('success', 'Periode baru berhasil dibuka.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Period $period)
    {
        return view('periods.show', compact('period'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Period $period)
    {
        return view('periods.edit', compact('period'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Period $period)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:open,closed,locked',
        ]);

        $data = $request->only(['name', 'start_date', 'end_date', 'status']);

        if ($request->status == 'closed' && $period->status == 'open') {
            $data['closed_at'] = now();
            $data['closed_by'] = auth()->id();
        }

        $period->update($data);

        return redirect()->route('periods.index')
            ->with('success', 'Periode berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Period $period)
    {
        if ($period->status != 'open') {
            return back()->with('error', 'Hanya periode OPEN yang dapat dihapus.');
        }

        $period->delete();

        return redirect()->route('periods.index')
            ->with('success', 'Periode berhasil dihapus.');
    }
}
