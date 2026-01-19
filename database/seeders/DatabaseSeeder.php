<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create dummy users for each role
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@innotrack.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Staff Member',
                'email' => 'staff@innotrack.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Student User',
                'email' => 'student@innotrack.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create sample items
        $items = [
            [
                'name' => 'Digital Microscope',
                'category' => 'Laboratory Equipment',
                'total_stock' => 5,
                'available_stock' => 5,
                'description' => 'High-resolution digital microscope for detailed observations.',
                'image_path' => null,
            ],
            [
                'name' => 'pH Meter',
                'category' => 'Testing Equipment',
                'total_stock' => 10,
                'available_stock' => 8,
                'description' => 'Digital pH meter for accurate acidity measurements.',
                'image_path' => null,
            ],
            [
                'name' => 'Laboratory Scale',
                'category' => 'Weighing Equipment',
                'total_stock' => 3,
                'available_stock' => 2,
                'description' => 'Precision laboratory scale for accurate measurements.',
                'image_path' => null,
            ],
            [
                'name' => 'Bunsen Burner',
                'category' => 'Heating Equipment',
                'total_stock' => 15,
                'available_stock' => 12,
                'description' => 'Gas burner for heating and sterilization.',
                'image_path' => null,
            ],
            [
                'name' => 'Centrifuge',
                'category' => 'Laboratory Equipment',
                'total_stock' => 2,
                'available_stock' => 1,
                'description' => 'High-speed centrifuge for sample separation.',
                'image_path' => null,
            ],
            [
                'name' => 'Pipette Set',
                'category' => 'Measurement Tools',
                'total_stock' => 20,
                'available_stock' => 18,
                'description' => 'Precision pipette set for accurate liquid measurements.',
                'image_path' => null,
            ],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }

        $this->command->info('Database seeded successfully!');
    }
}
