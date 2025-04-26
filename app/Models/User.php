<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'role',
        'department_id',
        'last_login_time',
        'failed_login_attempts',
        'is_locked',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_locked' => 'boolean',
        'last_login_time' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments', 'user_id', 'task_id')
                    ->withTimestamps();
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by', 'user_id');
    }
}
