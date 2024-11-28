<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Event;
use Illuminate\Database\Seeder;
use App\Models\MasterList;
use App\Models\MasterListStudent;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // User::factory()->create([
        //     'type' => 'facilitator',
        //     'email' => 'ryan.cuarez@csucc.edu.ph',
        //     'fname' => 'Ryan',
        //     'lname' => "Cuarez"
        // ]); //if no password specfied default is "password"


        // User::factory()->create([
        //     'type' => 'admin',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password')
        // ]);
        // User::factory()->create([
        //     'type' => 'admin',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password')
        // ]);



        // Event::factory()->create(
        //     [
        //         'id' => 1,
        //         'user_id' => 1,
        //         'code' => 'test1',
        //         'name' => 'test1',
        //         'description' => 'test1',
        //         'location' => 'test1',
        //         'profile_image' => 'test1',
        //         'is_restricted' => true
        //     ]
        // );

        // Event::factory()->create(
        //     [
        //         'id' => 2,
        //         'user_id' => 2,
        //         'code' => 'test2',
        //         'name' => 'test2',
        //         'description' => 'test2',
        //         'location' => 'test2',
        //         'profile_image' => 'test2',
        //         'is_restricted' => true
        //     ]
        // );

        // MasterList::factory()->create(
        //     [
        //         'user_id' => 1,
        //        'event_id' => 1,
        //        'name' => "Test class attendance masterlist"
        //     ]
        // );

        // MasterListStudent::factory()->create(
        //     [
        //         'user_id' => 2,
        //         'master_list_id' => 1,
        //     ]
        // );
    }
}
