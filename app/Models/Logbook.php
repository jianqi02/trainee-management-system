<?php

namespace App\Models;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logbook extends Model
{
    use HasFactory;
    protected $table = 'logbooks';
    protected $fillable = [
        'logbook_path',
        'trainee_id',
        'status',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function traineeKey()
    {
        return $this->belongsTo(AllTrainee::class, 'trainee_id'); //foreign key
    }
}
