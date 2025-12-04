<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'class_id',
        'user_id',
        'order_number',
        'course_id',
        'type',
        'email',
        'payment_status',
        'created_by',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function event()
    {
        return $this->belongsTo(Events::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
