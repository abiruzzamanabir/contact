<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'changed_data',
        'performed_by',
        'performed_at'
    ];

    protected $casts = [
        'changed_data' => 'array',
        'performed_at' => 'datetime',
    ];
}
