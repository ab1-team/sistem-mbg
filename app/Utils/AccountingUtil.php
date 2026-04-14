<?php

namespace App\Utils;

use App\Models\Account;
use App\Models\AkunLevel1;
use App\Models\ArusKas;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class AccountingUtil
{
    public static function sumSaldo($account, $bulan = '00', $tahun = null): float
    {
        $saldo = 0;
        $tahun = $tahun ?? date('Y');

        if ($account) {
            // Ensure balance is loaded for the correct year
            if (! $account->relationLoaded('balance') || ($account->balance && $account->balance->tahun != $tahun)) {
                $account->load(['balance' => fn ($q) => $q->where('tahun', $tahun)]);
            }

            if ($account->balance) {
                $bulan = intval($bulan);
                for ($i = 0; $i <= $bulan; $i++) {
                    $kolomDebit = 'debit_'.str_pad($i, 2, '0', STR_PAD_LEFT);
                    $kolomKredit = 'kredit_'.str_pad($i, 2, '0', STR_PAD_LEFT);

                    $saldoAkun = ($account->balance->$kolomDebit ?? 0) - ($account->balance->$kolomKredit ?? 0);
                    if ($account->jenis_mutasi == 'kredit') {
                        $saldoAkun = ($account->balance->$kolomKredit ?? 0) - ($account->balance->$kolomDebit ?? 0);
                    }

                    $saldo += $saldoAkun;
                }
            }
        }

        return (float) $saldo;
    }

    public static function saldoKas($tahun, $bulan): float
    {
        $accounts = Account::where('kode', 'LIKE', '1.1.01.%')->with([
            'balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->get();

        $saldo = 0;
        foreach ($accounts as $account) {
            $saldo += self::sumSaldo($account, $bulan, $tahun);
        }

        return (float) $saldo;
    }

    public static function saldoLabaRugi($tahun, $bulan = '00'): float
    {
        $return = 0;
        $labaRugi = self::labaRugi($tahun, $bulan);
        foreach ($labaRugi as $lr) {
            $return = $lr['total'];
        }

        return (float) $return;
    }

    public static function labaRugi($tahun, $bulan = '00', $dapurId = null): array
    {
        $akunLevel1s = AkunLevel1::where('kode', '>=', '4')->with([
            'level2s.level3s.accounts' => function ($query) use ($dapurId) {
                if ($dapurId && $dapurId !== 'all') {
                    $query->where('dapur_id', $dapurId);
                }
            },
            'level2s.level3s.accounts.balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->get();

        $akunPersediaans = Account::where('kode', '1.1.03.01')
            ->when($dapurId && $dapurId !== 'all', fn ($q) => $q->where('dapur_id', $dapurId))
            ->with([
                'balance' => function ($query) use ($tahun) {
                    $query->where('tahun', $tahun);
                },
            ])->get();

        $group = [
            '1' => ['nama' => 'Laba Kotor', 'jumlah' => 0, 'total' => 0, 'kode' => []],
            '2' => ['nama' => 'Pendapatan Lain Lain', 'jumlah' => 0, 'total' => 0, 'kode' => []],
            '3' => ['nama' => 'Beban Operasional', 'jumlah' => 0, 'total' => 0, 'kode' => []],
            '4' => ['nama' => 'Pendapatan Non Usaha', 'jumlah' => 0, 'total' => 0, 'kode' => []],
            '5' => ['nama' => 'Beban Non Usaha', 'jumlah' => 0, 'total' => 0, 'kode' => []],
            '6' => ['nama' => 'Beban Pajak', 'jumlah' => 0, 'total' => 0, 'kode' => []],
        ];

        $invKey = '1.1.03.01';
        if ($akunPersediaans->isNotEmpty()) {
            $saldo_bulan_ini = 0;
            $saldo_bulan_lalu = 0;
            $saldo_tahun_lalu = 0;
            foreach ($akunPersediaans as $acc) {
                $saldo_bulan_ini += self::sumSaldo($acc, $bulan, $tahun);
                $saldo_bulan_lalu += self::sumSaldo($acc, $bulan - 1, $tahun);
                $saldo_tahun_lalu += self::sumSaldo($acc, '00', $tahun);
            }

            $group[$invKey] = [
                'kode' => $akunPersediaans->first()->kode,
                'nama' => $akunPersediaans->first()->nama,
                'saldo_bulan_ini' => $saldo_bulan_ini,
                'saldo_bulan_lalu' => $saldo_bulan_lalu,
                'saldo_tahun_lalu' => $saldo_tahun_lalu,
            ];
        }

        foreach ($akunLevel1s as $akunLevel1) {
            foreach ($akunLevel1->level2s as $akunLevel2) {
                foreach ($akunLevel2->level3s as $akunLevel3) {
                    foreach ($akunLevel3->accounts as $account) {
                        $kode = $account->kode;
                        $parts = explode('.', $kode);
                        $kode1 = $parts[0] ?? '';
                        $kode2 = $parts[1] ?? '';

                        $saldo_bulan_ini = self::sumSaldo($account, $bulan, $tahun);
                        $saldo_bulan_lalu = self::sumSaldo($account, $bulan - 1, $tahun);
                        $saldo_tahun_lalu = self::sumSaldo($account, '00', $tahun);

                        $saldoData = [
                            'kode' => $kode,
                            'nama' => $account->nama,
                            'saldo_bulan_ini' => $saldo_bulan_ini,
                            'saldo_bulan_lalu' => $saldo_bulan_lalu,
                            'saldo_tahun_lalu' => $saldo_tahun_lalu,
                        ];

                        if (($kode1 == '4' || $kode1 == '5') && $kode != '4.1.01.05') {
                            if ($kode == '4.1.01.04' || $kode == '5.1.01.01') {
                                continue;
                            }
                            if ($kode == '5.1.01.02' && isset($group[$invKey])) {
                                $group['1']['kode'][] = $group[$invKey];
                                unset($group[$invKey]);
                            }
                            $group['1']['kode'][] = $saldoData;
                        } elseif ($kode1 == '6') {
                            $group['3']['kode'][] = $saldoData;
                        } elseif ($kode1 == '7' && $kode2 <= '2') {
                            $group['4']['kode'][] = $saldoData;
                        } elseif ($kode1 == '7' && $kode2 == '3') {
                            $group['5']['kode'][] = $saldoData;
                        } elseif ($kode1 == '7' && $kode2 == '4') {
                            $group['6']['kode'][] = $saldoData;
                        } elseif ($kode == '4.1.01.05') {
                            $group['2']['kode'][] = $saldoData;
                        }
                    }
                }
            }
        }

        $labaRugiArr = [];
        foreach ($group as $key => $value) {
            if (! is_numeric($key)) {
                continue;
            }

            $child = [];
            $kelompokAkun = [];
            $penjualanBersihBulanIni = 0;
            $totalSaldo = 0;

            foreach ($value['kode'] as $kode) {
                if ($kode['kode'] == '1.1.03.01') {
                    $saldoPenjualanBersih = ['saldo_bulan_ini' => 0, 'saldo_bulan_lalu' => 0, 'saldo_tahun_lalu' => 0];
                    foreach ($child as $ch) {
                        $saldoPenjualanBersih['saldo_bulan_ini'] += $ch['saldo_bulan_ini'] ?? 0;
                        $saldoPenjualanBersih['saldo_bulan_lalu'] += $ch['saldo_bulan_lalu'] ?? 0;
                        $saldoPenjualanBersih['saldo_tahun_lalu'] += $ch['saldo_tahun_lalu'] ?? 0;
                    }

                    $penjualanBersihBulanIni = $saldoPenjualanBersih['saldo_bulan_ini'];
                    $child[] = [
                        'kode' => '', 'nama' => 'Penjualan Bersih',
                        'saldo_bulan_ini' => $saldoPenjualanBersih['saldo_bulan_ini'],
                        'saldo_bulan_lalu' => $saldoPenjualanBersih['saldo_bulan_lalu'],
                        'saldo_tahun_lalu' => $saldoPenjualanBersih['saldo_tahun_lalu'],
                    ];

                    $kelompokAkun = [];
                    $persediaanAwal = [
                        'kode' => '', 'nama' => 'Persediaan Awal',
                        'saldo_bulan_ini' => $kode['saldo_bulan_lalu'],
                        'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0',
                    ];
                    $kode['saldo_bulan_ini'] -= $kode['saldo_bulan_lalu'];
                    $child[] = $persediaanAwal;
                    $kelompokAkun[] = $persediaanAwal;
                }

                $child[] = $kode;
                $kelompokAkun[] = $kode;

                if ($kode['kode'] == '5.1.01.06') {
                    $persAwalBI = 0;
                    $returPem = 0;
                    $totPem = 0;
                    $pres = 0;
                    foreach ($kelompokAkun as $kel) {
                        if ($kel['kode'] == '' || $kel['kode'] == '5.1.01.03') {
                            if ($kel['kode'] == '') {
                                $persAwalBI += $kel['saldo_bulan_ini'];
                            }
                            if ($kel['kode'] == '5.1.01.03') {
                                $returPem += $kel['saldo_bulan_ini'];
                            }

                            continue;
                        }
                        if ($kel['kode'] == '1.1.03.01') {
                            $pres += $kel['saldo_bulan_ini'];
                        }
                        $totPem += $kel['saldo_bulan_ini'];
                    }

                    $child[] = ['kode' => '', 'nama' => 'Total Pembelian', 'saldo_bulan_ini' => $totPem, 'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0'];
                    $child[] = ['kode' => '', 'nama' => 'Total Persediaan', 'saldo_bulan_ini' => $totPem + $persAwalBI, 'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0'];
                    $child[] = ['kode' => '', 'nama' => 'Persediaan Akhir', 'saldo_bulan_ini' => $persAwalBI + $pres + $returPem, 'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0'];

                    $hpp = (($totPem + $persAwalBI) - ($persAwalBI + $pres + $returPem));
                    $child[] = ['kode' => '', 'nama' => 'Harga Pokok Penjualan', 'saldo_bulan_ini' => $hpp, 'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0'];
                    $child[] = ['kode' => '', 'nama' => 'Laba Kotor', 'saldo_bulan_ini' => $penjualanBersihBulanIni - $hpp, 'saldo_bulan_lalu' => '0', 'saldo_tahun_lalu' => '0'];

                    $totalSaldo += ($penjualanBersihBulanIni - $hpp);
                }

                if ($key > 1) {
                    $totalSaldo += $kode['saldo_bulan_ini'];
                }
            }

            $group[$key]['jumlah'] = $totalSaldo;
            $group[$key]['total'] = $totalSaldo;
            if ($key > 1) {
                $group[$key]['total'] += $group[$key - 1]['total'];
            }

            $group[$key]['kode'] = $child;
            $labaRugiArr[] = $group[$key];
        }

        return $labaRugiArr;
    }

    public static function neraca($tahun, $bulan, $dapurId = null): array
    {
        return AkunLevel1::with([
            'level2s.level3s.accounts' => function ($query) use ($dapurId) {
                if ($dapurId && $dapurId !== 'all') {
                    $query->where('dapur_id', $dapurId);
                }
            },
            'level2s.level3s.accounts.balance' => function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            },
        ])->where('kode', '<', '4')->get()->all();
    }

    public static function arusKas(string $tanggalMulai, string $tanggalAkhir, $dapurId = null)
    {
        $semuaArusKas = ArusKas::with('rekenings')->orderBy('id')->get()->keyBy('id');
        $leafNodes = $semuaArusKas->filter(fn ($a) => $a->rekenings->isNotEmpty());
        $semuaArusKas->each(fn ($a) => $a->total = 0);

        if ($leafNodes->isNotEmpty()) {
            $cases = 'CASE ';
            $bindings = [];
            foreach ($leafNodes as $arusKas) {
                $whens = $arusKas->rekenings->map(function ($r) use (&$bindings) {
                    $bindings[] = $r->rekening_debit;
                    $bindings[] = $r->rekening_kredit;

                    return '(rekening_debit LIKE ? AND rekening_kredit LIKE ?)';
                })->implode(' OR ');
                $cases .= "WHEN {$whens} THEN {$arusKas->id} ";
            }
            $cases .= 'END';

            $innerQuery = Payment::selectRaw("{$cases} as arus_kas_id, total_harga", $bindings)
                ->whereRaw("{$cases} IS NOT NULL", $bindings)
                ->whereBetween('tanggal_pembayaran', [$tanggalMulai, $tanggalAkhir])
                ->when($dapurId && $dapurId !== 'all', fn ($q) => $q->where('dapur_id', $dapurId));

            $totals = Payment::selectRaw('arus_kas_id, SUM(total_harga) as total')
                ->fromSub($innerQuery, 'grouped')
                ->groupBy('arus_kas_id')
                ->pluck('total', 'arus_kas_id');

            foreach ($leafNodes as $id => $arusKas) {
                $arusKas->total = (float) ($totals->get($id) ?? 0);
            }
        }

        $visited = [];
        $aggregate = function ($node) use (&$aggregate, $semuaArusKas, &$visited) {
            if (isset($visited[$node->id])) {
                return;
            }
            $visited[$node->id] = true;
            $children = $semuaArusKas->filter(fn ($n) => $n->sub == $node->id || $n->super_sub == $node->id);
            foreach ($children as $child) {
                $aggregate($child);
                $node->total += $child->total;
            }
        };

        $semuaArusKas->each(fn ($node) => $aggregate($node));

        $result = collect();
        $curSection = null;
        $curGroup = null;
        foreach ($semuaArusKas->sortBy('id') as $node) {
            $isHeader = $node->sub == 0 && $node->super_sub != 0;
            $isSubHeader = $node->sub == 0 && $node->rekenings->isEmpty() && ! $isHeader;
            $isLeaf = ! $isHeader && ! $isSubHeader;

            if ($isHeader) {
                if ($curGroup !== null) {
                    $curSection['groups']->push($curGroup);
                    $curGroup = null;
                }
                if ($curSection !== null) {
                    $result->push($curSection);
                }
                $curSection = ['header' => $node, 'groups' => collect()];
            } elseif ($isSubHeader) {
                if ($curGroup !== null && $curSection !== null) {
                    $curSection['groups']->push($curGroup);
                }
                if ($curSection === null) {
                    $curSection = ['header' => null, 'groups' => collect()];
                }
                $curGroup = ['subheader' => $node, 'items' => collect()];
            } elseif ($isLeaf) {
                if ($curGroup === null) {
                    $curGroup = ['subheader' => null, 'items' => collect()];
                }
                $curGroup['items']->push($node);
            }
        }
        if ($curGroup !== null && $curSection !== null) {
            $curSection['groups']->push($curGroup);
        }
        if ($curSection !== null) {
            $result->push($curSection);
        }

        return $result;
    }

    public static function bukuBesar($accountId, $tahun, $bulan, $hari = '-')
    {
        $account = Account::with(['balance' => fn ($q) => $q->where('tahun', $tahun)])
            ->where('id', $accountId)
            ->first();

        if (! $account) {
            return null;
        }

        $kodeAkun = $account->kode;
        $bulanInt = intval($bulan);

        $saldoAwalDebit = $account->balance->debit_00 ?? 0;
        $saldoAwalKredit = $account->balance->kredit_00 ?? 0;
        $saldoAwal = $saldoAwalDebit - $saldoAwalKredit;

        $bulanLalu = $bulanInt - 1;
        $saldoBulanLaluDebit = 0;
        $saldoBulanLaluKredit = 0;
        $saldoBulanLalu = 0;
        if ($bulanLalu > 0) {
            $debitBulanLalu = 'debit_'.str_pad($bulanLalu, 2, '0', STR_PAD_LEFT);
            $kreditBulanLalu = 'kredit_'.str_pad($bulanLalu, 2, '0', STR_PAD_LEFT);
            $saldoBulanLaluDebit = $account->balance->$debitBulanLalu ?? 0;
            $saldoBulanLaluKredit = $account->balance->$kreditBulanLalu ?? 0;
            $saldoBulanLalu = $saldoBulanLaluDebit - $saldoBulanLaluKredit;
        }

        $query = Payment::whereYear('tanggal_pembayaran', $tahun)
            ->whereMonth('tanggal_pembayaran', $bulan)
            ->where(function ($q) use ($kodeAkun) {
                $q->where('rekening_debit', 'like', $kodeAkun.'%')
                    ->orWhere('rekening_kredit', 'like', $kodeAkun.'%');
            });

        if ($hari !== '-') {
            $query->whereDay('tanggal_pembayaran', $hari);
        }

        $payments = $query->orderBy('tanggal_pembayaran', 'asc')->get();

        return [
            'akun' => $account,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'namaBulan' => Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM'),
            'saldoAwalDebit' => $saldoAwalDebit,
            'saldoAwalKredit' => $saldoAwalKredit,
            'saldoAwal' => $saldoAwal,
            'saldoBulanLaluDebit' => $saldoBulanLaluDebit,
            'saldoBulanLaluKredit' => $saldoBulanLaluKredit,
            'saldoBulanLalu' => $saldoBulanLalu,
            'payments' => $payments,
        ];
    }
}
