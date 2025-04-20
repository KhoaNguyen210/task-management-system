<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';
    public $incrementing = true;
    protected $fillable = ['name', 'head_id'];

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id', 'user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }
}