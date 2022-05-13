<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = [
        'session',
        'label',
        'institute',
        'degree',
        'subject',
        'result',
    ];
}
