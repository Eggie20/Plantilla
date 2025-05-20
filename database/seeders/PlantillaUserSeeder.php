<?php

namespace Database\Seeders;

use App\Models\PlantillaUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlantillaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        PlantillaUser::create([
            'username' => 'cris.las',
            'password' => Hash::make('0000-0000'),
            'name' => 'Administrator',
            'role' => 'admin',
            'permissions' => ['view', 'create', 'edit', 'delete', 'approve']
        ]);

        // Create Regular User
        PlantillaUser::create([
            'username' => 'user',
            'password' => Hash::make('user123'),
            'name' => 'Regular User',
            'role' => 'user',
            'permissions' => ['view']
        ]);
    }
}