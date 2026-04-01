<?php

namespace App\Livewire;

use App\Models\MenuPeriod;
use App\Notifications\MenuPeriodStatusChanged;
use Livewire\Component;

class MenuPeriodActions extends Component
{
    public MenuPeriod $menuPeriod;

    public $rejection_note = '';

    public $showRejectModal = false;

    public function submit()
    {
        $this->menuPeriod->update([
            'status' => MenuPeriod::STATUS_PENDING,
        ]);

        // Notify Admins (Future: Notify all users with admin role)
        // For now, notification logic is prepared

        session()->flash('success', 'Rencana Menu telah diajukan untuk approval.');

        return redirect()->route('menu-periods.show', $this->menuPeriod);
    }

    public function approve()
    {
        $this->menuPeriod->update([
            'status' => MenuPeriod::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Notify Creator
        $this->menuPeriod->creator->notify(new MenuPeriodStatusChanged($this->menuPeriod, 'disetujui'));

        session()->flash('success', 'Rencana Menu telah disetujui.');

        return redirect()->route('menu-periods.show', $this->menuPeriod);
    }

    public function reject()
    {
        $this->validate([
            'rejection_note' => 'required|string|min:5',
        ]);

        $this->menuPeriod->update([
            'status' => MenuPeriod::STATUS_REJECTED,
            'rejection_note' => $this->rejection_note,
        ]);

        // Notify Creator
        $this->menuPeriod->creator->notify(new MenuPeriodStatusChanged($this->menuPeriod, 'ditolak'));

        $this->showRejectModal = false;
        session()->flash('success', 'Rencana Menu telah ditolak.');

        return redirect()->route('menu-periods.show', $this->menuPeriod);
    }

    public function render()
    {
        return view('livewire.menu-period-actions');
    }
}
