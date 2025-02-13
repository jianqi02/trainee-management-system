<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'comments';
    protected $fillable = [
        'comment',
        'trainee_id',
        'supervisor_id',
    ];
}
