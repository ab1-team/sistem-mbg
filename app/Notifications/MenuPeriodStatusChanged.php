<?php

namespace App\Notifications;

use App\Models\MenuPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MenuPeriodStatusChanged extends Notification
{
    use Queueable;

    protected $menuPeriod;

    protected $status;

    public function __construct(MenuPeriod $menuPeriod, $status)
    {
        $this->menuPeriod = $menuPeriod;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $messages = [
            'disetujui' => "Rencana menu '{$this->menuPeriod->title}' telah disetujui oleh Admin.",
            'ditolak' => "Rencana menu '{$this->menuPeriod->title}' ditolak. Alasan: ".($this->menuPeriod->rejection_note ?? 'Tidak ada catatan.'),
            'menunggu_approval' => "Rencana menu baru '{$this->menuPeriod->title}' menunggu persetujuan Anda.",
        ];

        return [
            'menu_period_id' => $this->menuPeriod->id,
            'title' => 'Update Status Perencanaan',
            'message' => $messages[$this->status] ?? "Status rencana menu berubah menjadi {$this->status}.",
            'status' => $this->status,
            'url' => route('menu-periods.show', $this->menuPeriod),
        ];
    }
}
