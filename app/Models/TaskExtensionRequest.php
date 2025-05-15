<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskExtensionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'reason',
        'new_due_date',
        'status',
        'approved_by',
        'comment', // Thêm cột mới
    ];

    protected $casts = [
        'new_due_date' => 'date',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}