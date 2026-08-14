<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ParcelaVencendoNotification extends Notification
{
    public function __construct(
        private int $vencendoHoje,
        private int $atrasadas,
        private float $totalVencendoHoje,
        private float $totalAtrasado
    ) {
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $partes = [];

        if ($this->vencendoHoje > 0) {
            $partes[] = "{$this->vencendoHoje} parcela(s) vencendo hoje (R$ " . number_format($this->totalVencendoHoje, 2, ',', '.') . ')';
        }

        if ($this->atrasadas > 0) {
            $partes[] = "{$this->atrasadas} parcela(s) atrasada(s) (R$ " . number_format($this->totalAtrasado, 2, ',', '.') . ')';
        }

        return (new WebPushMessage())
            ->title('Gerenciador de Finanças')
            ->icon('/icons/favicon-180.png')
            ->body(implode(' | ', $partes))
            ->data(['url' => url('/emprestimos')])
            ->options(['TTL' => 3600]);
    }
}
