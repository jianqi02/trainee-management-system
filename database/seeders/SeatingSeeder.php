<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('seatings')->insert([
            [
                'seat_name' => 'T1',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'T2',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM01',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM02',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM03',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM04',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM05',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM06',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM07',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM08',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM09',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'CSM10',
                'seat_trainee' => '',
            ],
            [
                'seat_name' => 'Round-Table',
                'seat_trainee' => '',
            ],
        ]);
    }
}
