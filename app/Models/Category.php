<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'category_courses', 'category_id', 'course_id');
    }
}
