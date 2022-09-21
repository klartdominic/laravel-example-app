<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = UserStatus::where('name', config('user.statuses.active'))->first();

        // create the system admin
        $this->_createSystemAdmin();
    }

    private function _createSystemAdmin()
    {
        // retrieve user status
         $status = UserStatus::where('name', config('user.statuses.active'))->first();

        // create the system admin
        User::create([
            'id' => 1,
            'name' => 'Sprobe',
            'email' => 'test@user.com',
            'password' => Hash::make('Password2020!'),
            'last_login' => Carbon::now(),
            'status_id' => $status->id,
            'remember_token' => 'none',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}