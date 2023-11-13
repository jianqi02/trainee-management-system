<?php

namespace Database\Seeders;

use App\Models\Trainee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TraineeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trainees')->insert([
            [
                'name' => 'Gentol Kurniawan',
                'personal_email' => 'Shinkurnia@example.com',
                'sains_email' => 'gk@sains.com.my',
                'phone_number' => '+60 000000001',
                'password' => '',
                'internship_start' => '2023-07-17',
                'internship_end' => '2023-12-28',
                'graduate_date' => '2025-03-12',
                'expertise' => 'Computer Security',
            ],
            [
                'name' => 'Micheal Sherlasher',
                'personal_email' => 'mic4023@example.com',
                'sains_email' => 'ms@sains.com.my',
                'phone_number' => '+60 769302',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Computer Security',
            ],
            [
                'name' => 'Newtoski Humo Schersmac',
                'personal_email' => 'newtoski66@example.com',
                'sains_email' => 'nhs@sains.com.my',
                'phone_number' => '+60 12345676688',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Networking',
            ],
            [
                'name' => 'Carol Celline',
                'personal_email' => 'ccline65@example.com',
                'sains_email' => 'cc@sains.com.my',
                'phone_number' => '+60 88883232113',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Multimedia Design',
            ],
            [
                'name' => 'Jackie Chew Man Teng',
                'personal_email' => 'jackiechew1023@example.com',
                'sains_email' => 'jcmt@sains.com.my',
                'phone_number' => '+60 682193298232',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Programming',
            ],
            [
                'name' => 'Shuma Tura',
                'personal_email' => 'smtra3411@example.com',
                'sains_email' => 'st@sains.com.my',
                'phone_number' => '+60 2222223355',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Others',
            ],
            [
                'name' => 'Koran Eran',
                'personal_email' => 'koran77@example.com',
                'sains_email' => 'ke@sains.com.my',
                'phone_number' => '+60 12312344555',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Multimedia Design',
            ],
            [
                'name' => 'Uranorea Tyiast Ama',
                'personal_email' => 'uranus939@example.com',
                'sains_email' => 'uta@sains.com.my',
                'phone_number' => '+60 2134664454',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Multimedia Design',
            ],
            [
                'name' => 'Yukari Mikasa',
                'personal_email' => 'yukariluv7610@example.com',
                'sains_email' => 'ym@sains.com.my',
                'phone_number' => '+60 5637888754',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Multimedia Design',
            ],
            [
                'name' => 'Justine Blankie',
                'personal_email' => 'cjustine12@example.com',
                'sains_email' => 'jb@sains.com.my',
                'phone_number' => '+60 78324324234',
                'password' => '',
                'internship_start' => '2023-07-15',
                'internship_end' => '2023-12-27',
                'graduate_date' => '2025-03-15',
                'expertise' => 'Multimedia Design',
            ],
        ]);

    }
}
