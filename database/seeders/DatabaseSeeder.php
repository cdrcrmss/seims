<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use App\Models\ProcurementRequest;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Clean all data except the three core accounts ────────────────
        $keepEmails = ['admin@SEIMS.com', 'staff@SEIMS.com', 'student@SEIMS.com'];

        DB::table('notifications')->delete();
        DB::table('audit_logs')->delete();
        DB::table('item_supplier')->delete();

        Borrowing::withTrashed()->forceDelete();
        ProcurementRequest::withTrashed()->forceDelete();
        DB::table('maintenance_records')->delete();
        Reservation::withTrashed()->forceDelete();
        Item::withTrashed()->forceDelete();
        Supplier::query()->delete();

        // Remove extra users but keep the three seeded accounts
        User::withTrashed()
            ->whereNotIn('email', $keepEmails)
            ->forceDelete();

        Room::withTrashed()->forceDelete();

        $this->command->info('✓ Existing data cleared');

        // ─── Users ────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@SEIMS.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@SEIMS.com'],
            [
                'name'     => 'Staff Member',
                'password' => Hash::make('password123'),
                'role'     => 'staff',
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@SEIMS.com'],
            [
                'name'     => 'Student User',
                'password' => Hash::make('password123'),
                'role'     => 'student',
            ]
        );

        $this->command->info('✓ Users seeded (3 accounts)');

        // ─── Rooms 401 – 418 ──────────────────────────────────────────────
        for ($i = 401; $i <= 418; $i++) {
            Room::create([
                'name'     => "Room $i",
                'code'     => "RM-$i",
                'building' => 'Main Building',
                'floor'    => '4',
                'capacity' => 40,
                'type'     => 'classroom',
                'status'   => 'available',
            ]);
        }

        $this->command->info('✓ Rooms seeded (Room 401 – 418)');

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  SEIMS Database Seeded Successfully');
        $this->command->info('  Login: admin@SEIMS.com  / password123');
        $this->command->info('         staff@SEIMS.com  / password123');
        $this->command->info('         student@SEIMS.com / password123');
        $this->command->info('═══════════════════════════════════════');
    }
}
