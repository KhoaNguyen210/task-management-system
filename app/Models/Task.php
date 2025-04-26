<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'created_by',
        'department_id',
    ];

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id')
                    ->withTimestamps();
    }

    protected $attributes = [
        'status' => 'Not Started',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];
}
