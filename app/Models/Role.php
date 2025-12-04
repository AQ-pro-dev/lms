<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const LEARNER_ROLE = 3; 
    public const TUTOR_ROLE = 2;   

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users');
    }
}
