<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeRequestNotification extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(
        public readonly int $exchangeRequestId,
        public readonly string $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'exchange_request_id' => $this->exchangeRequestId,
            'event' => $this->event,
            'message' => $this->message(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('แจ้งเตือนคำขอแลกเปลี่ยนทักษะ')
            ->greeting('สวัสดี '.$notifiable->name)
            ->line($this->message())
            ->action('ดูคำขอแลกเปลี่ยน', route('exchange-requests.show', $this->exchangeRequestId));
    }

    private function message(): string
    {
        return match ($this->event) {
            'created' => 'คุณได้รับคำขอแลกเปลี่ยนทักษะใหม่',
            'accepted' => 'คำขอแลกเปลี่ยนทักษะของคุณได้รับการตอบรับแล้ว',
            'rejected' => 'คำขอแลกเปลี่ยนทักษะของคุณถูกปฏิเสธ',
            'cancelled' => 'คำขอแลกเปลี่ยนทักษะถูกยกเลิก',
            'completed' => 'กิจกรรมแลกเปลี่ยนทักษะเสร็จสิ้นแล้ว',
            default => 'มีการอัปเดตคำขอแลกเปลี่ยนทักษะ',
        };
    }
}
