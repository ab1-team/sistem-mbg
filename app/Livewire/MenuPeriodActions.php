<?php

namespace App\Livewire;

use App\Models\MenuPeriod;
use App\Models\User;
use App\Notifications\MenuPeriodStatusChanged;
use App\Notifications\MenuPeriodSubmitted;
use Filament\Notifications\Notification;
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

        // Notify Kitchen Head & Admins
        $recipients = User::role(['kepala_dapur', 'admin_yayasan'])->get();
        foreach ($recipients as $recipient) {
            $recipient->notify(new MenuPeriodSubmitted($this->menuPeriod, auth()->user()->name));
        }

        Notification::make()
            ->title('Berhasil Diajukan')
            ->body('Rencana Menu telah diajukan untuk approval.')
            ->success()
            ->send();

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

        Notification::make()
            ->title('Rencana Disetujui')
            ->body('Rencana Menu telah disetujui.')
            ->success()
            ->send();

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
        Notification::make()
            ->title('Rencana Ditolak')
            ->body('Rencana Menu telah ditolak.')
            ->danger()
            ->send();

        return redirect()->route('menu-periods.show', $this->menuPeriod);
    }

    public function render()
    {
        return view('livewire.menu-period-actions');
    }
}
