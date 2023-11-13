<?php

namespace App\Models;

use App\Models\Supervisor;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllTrainee extends Model
{
    use HasFactory;
    protected $table = 'alltrainees';

    protected $fillable = [
        'name',
        'internship_start',
        'internship_end',
    ];

    public function traineeRecordExists()
    {
        $trainee = Trainee::where('name', $this->name)->first();
        return $trainee !== null;
    }

    public function seatings()
    {
        return $this->hasMany(Seating::class, 'trainee_id');
    }
}
