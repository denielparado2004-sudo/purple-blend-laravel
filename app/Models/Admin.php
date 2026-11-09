<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];

    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
