<?php

namespace App\Models;

use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TraineeAssign extends Model
{
    use HasFactory;
    protected $table = 'trainee_supervisors';
    protected $fillable = [
        'id',
        'trainee_id',
        'assigned_supervisor_id',
    ];


    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'assigned_supervisor_id'); //foreign key
    }
    
    public function trainee()
    {
        return $this->belongsTo(AllTrainee::class, 'trainee_id'); //foreign key
    }
}
