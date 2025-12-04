<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_file',
        'order',
        'video_duration',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function videoActivities()
    {
        return $this->hasMany(VideoActivity::class);
    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
