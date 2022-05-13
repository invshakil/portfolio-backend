<?php

namespace App\Models;

class PasswordReset extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
}
