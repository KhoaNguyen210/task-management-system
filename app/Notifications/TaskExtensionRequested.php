<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskExtensionRequested extends Notification
{
    use Queueable;

    protected $taskExtensionRequest;

    public function __construct($taskExtensionRequest)
    {
        $this->taskExtensionRequest = $taskExtensionRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $task = $this->taskExtensionRequest->task;
        return (new MailMessage)
                    ->subject('Yêu cầu gia hạn công việc: ' . $task->title)
                    ->line('Giảng viên ' . $this->taskExtensionRequest->user->name . ' đã gửi yêu cầu gia hạn cho công việc: ' . $task->title)
                    ->line('Lý do: ' . $this->taskExtensionRequest->reason)
                    ->line('Thời hạn mới đề xuất: ' . $this->taskExtensionRequest->new_due_date->format('d/m/Y'))
                    ->action('Xem chi tiết', url('/dashboard/department_head'));
    }
}