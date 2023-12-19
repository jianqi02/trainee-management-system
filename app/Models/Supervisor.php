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
        'personal_email',
        'sains_email',
        'phone_number',
        'trainee_status',
    ];

    public function traineeSupervisor()
    {
        return $this->hasMany(TraineeAssign::class, 'supervisor_id');
    }

    public function supervisor()
    {
        return $this->hasMany(Comment::class, 'supervisor_id');
    }
}
