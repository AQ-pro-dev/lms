<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'file_path',
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id')->whereHas('roles', function ($query) {
            $query->where('name', 'Tutor'); 
        });
    }
}
