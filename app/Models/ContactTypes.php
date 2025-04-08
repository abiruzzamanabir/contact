<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTypes extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_contact_type', 'contact_type_id', 'contact_id');
    }
}
