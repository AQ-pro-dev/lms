<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'microsoft_event_id',
        'title',
        'description',
        'capacity',
        'visibility',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function recordedLectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
