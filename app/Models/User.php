<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $fillable = [
        'username', 'password', 'name', 'email', 'role', 'department_id',
        'last_login_time', 'failed_login_attempts', 'is_locked',
    ];

    protected $hidden = ['password'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}