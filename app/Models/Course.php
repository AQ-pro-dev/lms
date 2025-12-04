<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail',
        'video_path',
        'learning_outcomes',
        'target_audience',
        'requirements',
        'is_drafted',
        'is_published',
        'is_paid',
        'price',
        'course_type',
        'is_completed',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function tutors()
    {
        return $this->belongsToMany(Tutor::class, 'course_tutors', 'course_id', 'user_id');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tags');
    }
    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'course_id', 'user_id')
            ->withTimestamps();
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'course_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_courses', 'course_id', 'category_id');
    }
    public function progress()
    {
        return $this->hasMany(CourseProgress::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
