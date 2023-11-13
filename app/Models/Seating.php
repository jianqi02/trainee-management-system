<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seating extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'seat_name',
        'trainee_id',
        'seat_status',
        'week',
        'start_date',
    ];
}
