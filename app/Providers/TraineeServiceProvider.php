<?php

namespace App\Providers;

use App\Models\Trainee;
use App\Models\AllTrainee;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class TraineeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $traineesWithEndedInternship = AllTrainee::whereNotNull('internship_end')
        ->whereDate('internship_end', '<', Carbon::now()->toDateString())
        ->get();
    
        foreach ($traineesWithEndedInternship as $data) {
            // Update the status to 'Inactive'
            Trainee::where('name', 'LIKE', $data->name)->update(['acc_status' => 'Inactive']);
        }
    }
}
