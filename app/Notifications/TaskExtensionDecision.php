<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class TaskExtensionDecision
 * 
 * Notification for task extension request decisions.
 */
class TaskExtensionDecision extends Notification
{
    use Queueable;

    protected $extensionRequest;
    protected $decision;

    /**
     * Create a new notification instance.
     *
     * @param mixed $extensionRequest The extension request instance
     * @param string $decision The decision ('approved' or 'rejected')
     */
    public function __construct($extensionRequest, $decision)
    {
        $this->extensionRequest = $extensionRequest;
        $this->decision = $decision;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
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