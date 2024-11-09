<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingImage extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'seating_images';

    // Fillable attributes
    protected $fillable = [
        'image_path',
        'week',
    ];
}
