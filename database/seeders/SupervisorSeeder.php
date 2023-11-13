<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('supervisors')->insert([
            [
                'name' => 'Chen Yu Han',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'yuhan0323@example.com',
                'sains_email' => 'cyh@sains.com.my',
                'phone_number' => '+60 745454545232',
                'password' => '',
            ],
            [
                'name' => 'Leo Nan Ran',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'leonan@example.com',
                'sains_email' => 'lnr@sains.com.my',
                'phone_number' => '+60 1223432321312',
                'password' => '',
            ],
            [
                'name' => 'Stephanie Rojand',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'stephie@example.com',
                'sains_email' => 'sr@sains.com.my',
                'phone_number' => '+60 10000203040',
                'password' => '',
            ],
            [
                'name' => 'Jepta Lyra',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'lyrasira@example.com',
                'sains_email' => 'jl@sains.com.my',
                'phone_number' => '+60 7213232',
                'password' => '',
            ],
            [
                'name' => 'Gojack Therapisty',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'gojack123@example.com',
                'sains_email' => 'gt@sains.com.my',
                'phone_number' => '+60 7007543322223',
                'password' => '',
            ],
            [
                'name' => 'Oman Lesklyiasck',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'omanpure2@example.com',
                'sains_email' => 'ol@sains.com.my',
                'phone_number' => '+60 2347888765',
                'password' => '',
            ],
            [
                'name' => 'Oswald Whitefield Garlando',
                'unit' => 'PSS',
                'department' => 'CSM',
                'personal_email' => 'oswaldclv6@example.com',
                'sains_email' => 'owg@sains.com.my',
                'phone_number' => '+60 1231919023',
                'password' => '',
            ],
            // Add more data as needed
        ]);
    }
}
