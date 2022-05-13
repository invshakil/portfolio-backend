<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = [
        'from',
        'to',
        'current',
        'company_name',
        'designation',
        'language',
        'description',
        'label'
    ];
}
