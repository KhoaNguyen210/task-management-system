<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskExtensionDecision extends Notification
{
    use Queueable;

    protected $extensionRequest;
    protected $decision;

    public function __construct($extensionRequest, $decision)
    {
        $this->extensionRequest = $extensionRequest;
        $this->decision = $decision;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $status = $this->decision === 'approved' ? 'được phê duyệt' : 'bị từ chối';
        $message = (new MailMessage)
                    ->subject('Quyết định về Yêu cầu Gia hạn: ' . $this->extensionRequest->task->title)
                    ->line('Yêu cầu gia hạn của bạn cho công việc "' . $this->extensionRequest->task->title . '" đã ' . $status . '.')
                    ->line('Thời hạn Mới Đề xuất: ' . $this->extensionRequest->new_due_date->format('d/m/Y'))
                    ->line('Lý do Gia hạn: ' . $this->extensionRequest->reason);

        if ($this->extensionRequest->comment) {
            $message->line('Nhận xét từ Trưởng Bộ môn: ' . $this->extensionRequest->comment);
        }

        $message->action('Xem Chi tiết', url('/tasks/' . $this->extensionRequest->task->id . '/show'));

        return $message;
    }
}