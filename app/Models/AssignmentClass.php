<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'class_id'
    ];

    public $timestamps = false;

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class);
    }
}
