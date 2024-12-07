<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\TraineeAssign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supervisor extends Model
{
    use HasFactory;
    protected $table = 'supervisors';


    protected $fillable = [
        'name',
        'section',
        'department',
        'expertise',
        'email',
        'trainee_status',
        'trainee_count',
    ];

    public function traineeSupervisor()
    {
        return $this->hasMany(TraineeAssign::class, 'supervisor_id');
    }

    public function supervisor()
    {
        return $this->hasMany(Comment::class, 'supervisor_id');
    }

    public function trainees()
    {
        return $this->belongsToMany(AllTrainee::class, 'trainee_supervisors', 'assigned_supervisor_id', 'trainee_id');
    }
}
