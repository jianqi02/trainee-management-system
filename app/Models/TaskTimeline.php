<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTimeline extends Model
{
    use HasFactory;
    protected $table = 'task_timelines';
    public $timestamps = false;

    protected $fillable = [
        'trainee_id',
        'task_name',
        'task_status',
        'task_start_date',
        'task_end_date',
        'task_detail',
        'timeline',
        'task_priority',
        'task_overall_comment',
    ];
}
