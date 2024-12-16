<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = ['title','creator_id', 'start','end'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'user_appointment');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
