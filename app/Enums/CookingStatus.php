<?php

namespace App\Enums;

enum CookingStatus: string
{
    case BELUM_MULAI = 'belum_mulai';
    case PERSIAPAN = 'persiapan';
    case MEMASAK = 'memasak';
    case SELESAI = 'selesai';
    case DIDISTRIBUSIKAN = 'didistribusikan';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_MULAI => 'Belum Mulai',
            self::PERSIAPAN => 'Persiapan',
            self::MEMASAK => 'Memasak',
            self::SELESAI => 'Selesai',
            self::DIDISTRIBUSIKAN => 'Didistribusikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BELUM_MULAI => 'bg-slate-100 text-slate-600 border-slate-200',
            self::PERSIAPAN => 'bg-amber-50 text-amber-700 border-amber-100',
            self::MEMASAK => 'bg-blue-50 text-blue-700 border-blue-100',
            self::SELESAI => 'bg-green-50 text-green-700 border-green-100',
            self::DIDISTRIBUSIKAN => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        };
    }

    /**
     * Definisi transisi yang valid sesuai alur dapur
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::BELUM_MULAI => [self::PERSIAPAN],
            self::PERSIAPAN => [self::MEMASAK, self::BELUM_MULAI],
            self::MEMASAK => [self::SELESAI],
            self::SELESAI => [self::DIDISTRIBUSIKAN],
            self::DIDISTRIBUSIKAN => [], // Final state
            default => []
        };
    }
}
