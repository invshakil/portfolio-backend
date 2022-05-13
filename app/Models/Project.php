<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = [
        'service_id',
        'name',
        'image',
        'tags',
        'demo_link',
        'description',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class,'service_id','id');
    }
}
