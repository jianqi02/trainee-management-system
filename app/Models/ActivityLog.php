<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Activity
{
    use HasFactory;
    protected $table = 'activity_log';
    protected $fillable = [
        'username',
        'action',
        'outcome',
        'details',
        'created_at',
        'updated_at'
    ];
}
