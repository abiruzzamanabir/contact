<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Contact extends Model
{
    use HasFactory, Notifiable;
    protected $guarded = [];
    protected $dates = ['deleted_at']; // Ensuring the deleted_at field is treated as a date
}
