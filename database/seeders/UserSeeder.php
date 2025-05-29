<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Raed Houssin ',
                'email' => 'raedhoussin33@gmail.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}