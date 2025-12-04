<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'entity_type',
        'entity_id',
        'data',
        'created_at'
    ];

    protected $casts = [
        'data' => 'json'
    ];

   
    public function entity()
    {
        return $this->morphTo();
    }
}
