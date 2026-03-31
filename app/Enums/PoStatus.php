<?php

namespace App\Enums;

enum PoStatus: string
{
    case DRAF = 'draf';
    case DIKIRIM_KE_YAYASAN = 'dikirim_ke_yayasan';
    case DIREVIEW_YAYASAN = 'direview_yayasan';
    case DITERUSKAN_KE_SUPPLIER = 'diteruskan_ke_supplier';
    case DIPROSES_SUPPLIER = 'diproses_supplier';
    case DALAM_PENGIRIMAN = 'dalam_pengiriman';
    case DITERIMA_SEBAGIAN = 'diterima_sebagian';
    case DITERIMA_LENGKAP = 'diterima_lengkap';
    case DITOLAK_YAYASAN = 'ditolak_yayasan';
    case DIBATALKAN = 'dibatalkan';
    case SELESAI = 'selesai';

    /**
     * Definisi transisi yang valid sesuai Roadmap 3.2
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DRAF => [self::DIKIRIM_KE_YAYASAN, self::DIBATALKAN],
            self::DIKIRIM_KE_YAYASAN => [self::DIREVIEW_YAYASAN, self::DITOLAK_YAYASAN, self::DIBATALKAN],
            self::DIREVIEW_YAYASAN => [self::DITERUSKAN_KE_SUPPLIER, self::DITOLAK_YAYASAN, self::DIBATALKAN],
            self::DITOLAK_YAYASAN => [self::DRAF, self::DIBATALKAN],
            self::DITERUSKAN_KE_SUPPLIER => [self::DIPROSES_SUPPLIER, self::DIBATALKAN],
            self::DIPROSES_SUPPLIER => [self::DALAM_PENGIRIMAN, self::DIBATALKAN],
            self::DALAM_PENGIRIMAN => [self::DITERIMA_SEBAGIAN, self::DITERIMA_LENGKAP],
            self::DITERIMA_SEBAGIAN => [self::DITERIMA_LENGKAP],
            self::DITERIMA_LENGKAP => [self::SELESAI],
            default => []
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DRAF => 'Draf',
            self::DIKIRIM_KE_YAYASAN => 'Dikirim ke Yayasan',
            self::DIREVIEW_YAYASAN => 'Direview Yayasan',
            self::DITERUSKAN_KE_SUPPLIER => 'Diteruskan ke Supplier',
            self::DIPROSES_SUPPLIER => 'Diproses Supplier',
            self::DALAM_PENGIRIMAN => 'Dalam Pengiriman',
            self::DITERIMA_SEBAGIAN => 'Diterima Sebagian',
            self::DITERIMA_LENGKAP => 'Diterima Lengkap',
            self::DITOLAK_YAYASAN => 'Ditolak Yayasan',
            self::DIBATALKAN => 'Dibatalkan',
            self::SELESAI => 'Selesai',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAF => 'bg-slate-100 text-slate-600 border-slate-200',
            self::DIKIRIM_KE_YAYASAN => 'bg-amber-50 text-amber-700 border-amber-100',
            self::DIREVIEW_YAYASAN => 'bg-blue-50 text-blue-700 border-blue-100',
            self::DITERUSKAN_KE_SUPPLIER => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            self::DIPROSES_SUPPLIER => 'bg-orange-50 text-orange-700 border-orange-100',
            self::DALAM_PENGIRIMAN => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            self::DITERIMA_SEBAGIAN => 'bg-teal-50 text-teal-700 border-teal-100',
            self::DITERIMA_LENGKAP => 'bg-green-50 text-green-700 border-green-100',
            self::DITOLAK_YAYASAN => 'bg-red-50 text-red-700 border-red-100',
            self::DIBATALKAN => 'bg-red-50 text-red-700 border-red-100',
            self::SELESAI => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        };
    }
}
