<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'buyer','guard_name' => 'api']);
        Role::create(['name' => 'seller','guard_name' => 'api']);
        Role::create(['name' => 'admin','guard_name' => 'api']);
    }
}
