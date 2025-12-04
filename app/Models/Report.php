<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'user_id',
        'attended',
        'join_time',
        'leave_time',
        'watch_time',
    ];

 
    public function class()
    {
        return $this->belongsTo(Classe::class);
    }

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
