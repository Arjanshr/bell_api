<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create(
            [
                'name' => 'Super Admin',
                'email' => 'super-admin@mobilemandu.com',
                'password' => bcrypt('password'),
            ]
        )->assignRole('super-admin');

       User::create(
            [
                'name' => 'Admin',
                'email' => 'admin@mobilemandu.com',
                'password' => bcrypt('password'),
            ]
        )->assignRole('admin');

        User::create(
            [
                'name' => 'Employee',
                'email' => 'employee@mobilemandu.com',
                'password' => bcrypt('password'),
            ]
        )->assignRole('employee');
        User::create(
            [
                'name' => 'Manager',
                'email' => 'manager@mobilemandu.com',
                'password' => bcrypt('password'),
            ]
        )->assignRole('manager');
        User::create(
            [
                'name' => 'Customer',
                'email' => 'customer@mobilemandu.com',
                'password' => bcrypt('password'),
            ]
        )->assignRole('customer');
    }
}
