<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoActivity extends Model
{
    protected $fillable = ['user_id', 'lecture_id', 'watched'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
