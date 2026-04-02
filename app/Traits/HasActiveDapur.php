<?php

namespace App\Traits;

use App\Models\Dapur;

trait HasActiveDapur
{
    /**
     * Get the active Dapur ID from subdomain or user profile.
     * Respects the "1 Subdomain = 1 Dapur" vision.
     */
    protected function getActiveDapurId(): ?int
    {
        // 1. Subdomain has highest priority
        if (session()->has('active_dapur_id')) {
            return session('active_dapur_id');
        }

        // 2. User's assigned Dapur as fallback
        return auth()->user()?->dapur_id;
    }

    /**
     * Scope a query to the active Dapur.
     */
    protected function scopeToActiveDapur($query)
    {
        $dapurId = $this->getActiveDapurId();

        if ($dapurId) {
            return $query->where('dapur_id', $dapurId);
        }

        return $query;
    }
}
