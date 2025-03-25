<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Passwords extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_name',
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
