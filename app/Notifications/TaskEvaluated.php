<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskEvaluated extends Notification
{
    use Queueable;

    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Công việc đã được đánh giá: ' . $this->task->title)
            ->line('Công việc "' . $this->task->title . '" của bạn đã được Trưởng Bộ môn đánh giá.')
            ->line('Mức độ hoàn thành: ' . $this->task->evaluation_level)
            ->line('Nhận xét: ' . ($this->task->evaluation_comment ?? 'Không có nhận xét'))
            ->action('Xem chi tiết', url('/tasks/' . $this->task->id . '/show'));
    }
}