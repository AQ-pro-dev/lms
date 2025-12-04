<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Events extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'microsoft_event_id',
        'title',
        'description',
        'capacity',
        'class_type',
        'teams_link',
        'onsite_address',
        'recorded_video_url',
        'is_paid',
        'price',
        'class_date',
        'class_time',
        'booking_start_date',
        'booking_end_date'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function recordedLectures()
    {
        return $this->hasMany(Lecture::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
