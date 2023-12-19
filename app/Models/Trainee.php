<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\TraineeAssign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trainee extends Model
{
    use HasFactory;
    protected $table = 'trainees';

    protected $fillable = [
        'name',
        'personal_email',
        'sains_email',
        'phone_number',
        'graduate_date',
        'expertise',
        'profile_image',
        'supervisor_status',
        'resume_path',
        'acc_status',
    ];

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'trainee_id', 'id');
    }

    public function traineeSupervisor()
    {
        return $this->hasMany(TraineeAssign::class, 'trainee_id');
    }

    public function trainee()
    {
        return $this->hasMany(Comment::class, 'trainee_id');
    }
}
