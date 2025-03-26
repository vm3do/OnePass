<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Ip extends Model
{
    protected $fillable = ['ip', 'status'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
