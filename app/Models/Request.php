<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'request_detail','subject','request_img', 'request_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
