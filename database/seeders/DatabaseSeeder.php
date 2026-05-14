<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use App\Models\ProcurementRequest;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Users ─────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@SEIMS.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@SEIMS.com',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@SEIMS.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $student2 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@SEIMS.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $student3 = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@SEIMS.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $this->command->info('✓ Users seeded');

        // ─── Items (with extended asset fields) ────────────
        $items = [
            [
                'name' => 'Digital Microscope',
                'category' => 'Laboratory Equipment',
                'total_stock' => 5,
                'available_stock' => 3,
                'description' => 'High-resolution digital microscope for detailed observations.',
                'asset_code' => 'INNO-EQ-001',
                'qr_code' => 'INNO-QR-000001',
                'asset_type' => 'equipment',
                'unit_price' => 45000.00,
                'low_stock_threshold' => 2,
                'reorder_quantity' => 3,
                'wear_level' => 35,
                'last_maintenance_date' => Carbon::now()->subDays(30)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(60)->toDateString(),
                'total_usage_count' => 128,
                'location' => 'Room 201 - Biology Lab',
                'purchase_date' => Carbon::now()->subMonths(8)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(16)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'pH Meter',
                'category' => 'Testing Equipment',
                'total_stock' => 10,
                'available_stock' => 7,
                'description' => 'Digital pH meter for accurate acidity measurements.',
                'asset_code' => 'INNO-EQ-002',
                'qr_code' => 'INNO-QR-000002',
                'asset_type' => 'equipment',
                'unit_price' => 8500.00,
                'low_stock_threshold' => 3,
                'reorder_quantity' => 5,
                'wear_level' => 20,
                'last_maintenance_date' => Carbon::now()->subDays(15)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(75)->toDateString(),
                'total_usage_count' => 256,
                'location' => 'Room 201 - Biology Lab',
                'purchase_date' => Carbon::now()->subMonths(6)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(18)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Laboratory Scale',
                'category' => 'Weighing Equipment',
                'total_stock' => 3,
                'available_stock' => 2,
                'description' => 'Precision laboratory scale for accurate measurements.',
                'asset_code' => 'INNO-EQ-003',
                'qr_code' => 'INNO-QR-000003',
                'asset_type' => 'equipment',
                'unit_price' => 25000.00,
                'low_stock_threshold' => 1,
                'reorder_quantity' => 2,
                'wear_level' => 55,
                'last_maintenance_date' => Carbon::now()->subDays(60)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(10)->toDateString(),
                'total_usage_count' => 340,
                'location' => 'Room 105 - Chemistry Lab',
                'purchase_date' => Carbon::now()->subMonths(14)->toDateString(),
                'warranty_expiry' => Carbon::now()->subMonths(2)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Bunsen Burner',
                'category' => 'Heating Equipment',
                'total_stock' => 15,
                'available_stock' => 12,
                'description' => 'Gas burner for heating and sterilization.',
                'asset_code' => 'INNO-EQ-004',
                'qr_code' => 'INNO-QR-000004',
                'asset_type' => 'equipment',
                'unit_price' => 3200.00,
                'low_stock_threshold' => 5,
                'reorder_quantity' => 10,
                'wear_level' => 10,
                'last_maintenance_date' => Carbon::now()->subDays(7)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(83)->toDateString(),
                'total_usage_count' => 512,
                'location' => 'Room 105 - Chemistry Lab',
                'purchase_date' => Carbon::now()->subMonths(3)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(21)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Centrifuge',
                'category' => 'Laboratory Equipment',
                'total_stock' => 2,
                'available_stock' => 1,
                'description' => 'High-speed centrifuge for sample separation.',
                'asset_code' => 'INNO-EQ-005',
                'qr_code' => 'INNO-QR-000005',
                'asset_type' => 'equipment',
                'unit_price' => 120000.00,
                'low_stock_threshold' => 1,
                'reorder_quantity' => 1,
                'wear_level' => 72,
                'last_maintenance_date' => Carbon::now()->subDays(90)->toDateString(),
                'next_maintenance_date' => Carbon::now()->subDays(5)->toDateString(),
                'total_usage_count' => 89,
                'location' => 'Room 201 - Biology Lab',
                'purchase_date' => Carbon::now()->subMonths(18)->toDateString(),
                'warranty_expiry' => Carbon::now()->subMonths(6)->toDateString(),
                'status' => 'maintenance',
            ],
            [
                'name' => 'Pipette Set',
                'category' => 'Measurement Tools',
                'total_stock' => 20,
                'available_stock' => 16,
                'description' => 'Precision pipette set for accurate liquid measurements.',
                'asset_code' => 'INNO-EQ-006',
                'qr_code' => 'INNO-QR-000006',
                'asset_type' => 'equipment',
                'unit_price' => 5600.00,
                'low_stock_threshold' => 5,
                'reorder_quantity' => 10,
                'wear_level' => 15,
                'last_maintenance_date' => Carbon::now()->subDays(20)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(70)->toDateString(),
                'total_usage_count' => 680,
                'location' => 'Room 105 - Chemistry Lab',
                'purchase_date' => Carbon::now()->subMonths(4)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(20)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Beaker Set (250ml)',
                'category' => 'Glassware',
                'total_stock' => 30,
                'available_stock' => 25,
                'description' => 'Borosilicate glass beaker set for general lab use.',
                'asset_code' => 'INNO-CS-007',
                'qr_code' => 'INNO-QR-000007',
                'asset_type' => 'consumable',
                'unit_price' => 150.00,
                'low_stock_threshold' => 10,
                'reorder_quantity' => 20,
                'wear_level' => 0,
                'total_usage_count' => 420,
                'location' => 'Room 105 - Chemistry Lab',
                'purchase_date' => Carbon::now()->subMonths(2)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Safety Goggles',
                'category' => 'Safety Equipment',
                'total_stock' => 50,
                'available_stock' => 42,
                'description' => 'Chemical splash-proof safety goggles.',
                'asset_code' => 'INNO-CS-008',
                'qr_code' => 'INNO-QR-000008',
                'asset_type' => 'consumable',
                'unit_price' => 350.00,
                'low_stock_threshold' => 15,
                'reorder_quantity' => 25,
                'wear_level' => 0,
                'total_usage_count' => 890,
                'location' => 'Room 101 - Storage',
                'purchase_date' => Carbon::now()->subMonths(1)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Fume Hood',
                'category' => 'Laboratory Equipment',
                'total_stock' => 3,
                'available_stock' => 2,
                'description' => 'Chemical fume extraction hood for safely handling volatile chemicals.',
                'asset_code' => 'INNO-EQ-009',
                'qr_code' => 'INNO-QR-000009',
                'asset_type' => 'equipment',
                'unit_price' => 250000.00,
                'low_stock_threshold' => 1,
                'reorder_quantity' => 1,
                'wear_level' => 45,
                'last_maintenance_date' => Carbon::now()->subDays(45)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(45)->toDateString(),
                'total_usage_count' => 200,
                'location' => 'Room 105 - Chemistry Lab',
                'purchase_date' => Carbon::now()->subMonths(12)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(12)->toDateString(),
                'status' => 'available',
            ],
            [
                'name' => 'Spectrophotometer',
                'category' => 'Testing Equipment',
                'total_stock' => 2,
                'available_stock' => 2,
                'description' => 'UV-Vis spectrophotometer for absorbance measurements.',
                'asset_code' => 'INNO-EQ-010',
                'qr_code' => 'INNO-QR-000010',
                'asset_type' => 'equipment',
                'unit_price' => 180000.00,
                'low_stock_threshold' => 1,
                'reorder_quantity' => 1,
                'wear_level' => 28,
                'last_maintenance_date' => Carbon::now()->subDays(10)->toDateString(),
                'next_maintenance_date' => Carbon::now()->addDays(80)->toDateString(),
                'total_usage_count' => 75,
                'location' => 'Room 201 - Biology Lab',
                'purchase_date' => Carbon::now()->subMonths(6)->toDateString(),
                'warranty_expiry' => Carbon::now()->addMonths(18)->toDateString(),
                'status' => 'available',
            ],
        ];

        $createdItems = [];
        foreach ($items as $itemData) {
            $createdItems[] = Item::create($itemData);
        }

        $this->command->info('✓ Items seeded (10 items with extended fields)');

        // ─── Rooms ─────────────────────────────────────────
        Room::query()->forceDelete();
        $rooms = [];
        for ($i = 401; $i <= 418; $i++) {
            $rooms[] = Room::create([
                'name' => 'Room ' . $i,
                'code' => 'RM-' . $i,
                'type' => 'classroom',
                'capacity' => 40,
                'floor' => '4th Floor',
                'building' => 'Main Building',
                'description' => '',
                'facilities' => [],
                'status' => 'available',
            ]);
        }

        $this->command->info('✓ Rooms seeded (18 rooms)');

        // ─── Suppliers ─────────────────────────────────────
        DB::table('item_supplier')->delete();
        Supplier::query()->forceDelete();

        $suppliers = [
            Supplier::create([
                'name' => 'LabTech Philippines Inc.',
                'code' => 'SUP-001',
                'contact_person' => 'Roberto Reyes',
                'email' => 'sales@labtechph.com',
                'phone' => '+63-2-8555-0101',
                'address' => '123 Science Ave, Quezon City',
                'city' => 'Quezon City',
                'country' => 'Philippines',
                'payment_terms' => 'Net 30',
                'delivery_time_days' => 7,
                'rating' => 4.5,
                'status' => 'active',
            ]),
            Supplier::create([
                'name' => 'MetroLab Supplies',
                'code' => 'SUP-002',
                'contact_person' => 'Ana Garcia',
                'email' => 'orders@metrolab.ph',
                'phone' => '+63-2-8777-0202',
                'address' => '456 Lab Road, Makati City',
                'city' => 'Makati City',
                'country' => 'Philippines',
                'payment_terms' => 'Net 15',
                'delivery_time_days' => 5,
                'rating' => 4.2,
                'status' => 'active',
            ]),
            Supplier::create([
                'name' => 'Global Scientific Corp.',
                'code' => 'SUP-003',
                'contact_person' => 'James Tan',
                'email' => 'info@globalscientific.com',
                'phone' => '+63-2-8333-0303',
                'address' => '789 Tech Park, Taguig City',
                'city' => 'Taguig City',
                'country' => 'Philippines',
                'payment_terms' => 'Net 45',
                'delivery_time_days' => 14,
                'rating' => 3.8,
                'status' => 'active',
            ]),
        ];

        // Link suppliers to items
        DB::table('item_supplier')->insert([
            ['item_id' => $createdItems[0]->id, 'supplier_id' => $suppliers[0]->id, 'unit_price' => 45000, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[0]->id, 'supplier_id' => $suppliers[2]->id, 'unit_price' => 48000, 'is_preferred' => false, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[1]->id, 'supplier_id' => $suppliers[1]->id, 'unit_price' => 8500, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[4]->id, 'supplier_id' => $suppliers[0]->id, 'unit_price' => 120000, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[5]->id, 'supplier_id' => $suppliers[1]->id, 'unit_price' => 5600, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[8]->id, 'supplier_id' => $suppliers[2]->id, 'unit_price' => 250000, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => $createdItems[9]->id, 'supplier_id' => $suppliers[0]->id, 'unit_price' => 180000, 'is_preferred' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✓ Suppliers seeded (3 suppliers + item links)');

        // ─── Borrowings (past and active) ──────────────────
        $borrowings = [
            Borrowing::create([
                'user_id' => $student->id,
                'item_id' => $createdItems[0]->id,
                'quantity' => 1,
                'status' => 'issued',
                'notes' => 'Cell biology experiment for Bio 101',
                'requested_date' => Carbon::now()->subDays(5)->toDateString(),
                'approved_date' => Carbon::now()->subDays(4)->toDateString(),
                'approved_by' => $staff->id,
                'issued_date' => Carbon::now()->subDays(3)->toDateString(),
                'issued_by' => $staff->id,
                'expected_return_date' => Carbon::now()->addDays(4)->toDateString(),
            ]),
            Borrowing::create([
                'user_id' => $student2->id,
                'item_id' => $createdItems[1]->id,
                'quantity' => 2,
                'status' => 'issued',
                'notes' => 'Water quality testing project',
                'requested_date' => Carbon::now()->subDays(7)->toDateString(),
                'approved_date' => Carbon::now()->subDays(6)->toDateString(),
                'approved_by' => $staff->id,
                'issued_date' => Carbon::now()->subDays(5)->toDateString(),
                'issued_by' => $staff->id,
                'expected_return_date' => Carbon::now()->subDays(1)->toDateString(), // overdue
            ]),
            Borrowing::create([
                'user_id' => $student3->id,
                'item_id' => $createdItems[5]->id,
                'quantity' => 2,
                'status' => 'pending',
                'notes' => 'Titration exercise for Chem 102',
                'requested_date' => Carbon::now()->toDateString(),
                'expected_return_date' => Carbon::now()->addDays(7)->toDateString(),
            ]),
            Borrowing::create([
                'user_id' => $student->id,
                'item_id' => $createdItems[3]->id,
                'quantity' => 1,
                'status' => 'returned',
                'notes' => 'Flame test experiment',
                'requested_date' => Carbon::now()->subDays(16)->toDateString(),
                'approved_date' => Carbon::now()->subDays(15)->toDateString(),
                'approved_by' => $staff->id,
                'issued_date' => Carbon::now()->subDays(14)->toDateString(),
                'issued_by' => $staff->id,
                'expected_return_date' => Carbon::now()->subDays(7)->toDateString(),
                'returned_date' => Carbon::now()->subDays(8)->toDateString(),
            ]),
            Borrowing::create([
                'user_id' => $student2->id,
                'item_id' => $createdItems[0]->id,
                'quantity' => 1,
                'status' => 'returned',
                'notes' => 'Microbiology slide preparation',
                'requested_date' => Carbon::now()->subDays(23)->toDateString(),
                'approved_date' => Carbon::now()->subDays(22)->toDateString(),
                'approved_by' => $staff->id,
                'issued_date' => Carbon::now()->subDays(21)->toDateString(),
                'issued_by' => $staff->id,
                'expected_return_date' => Carbon::now()->subDays(14)->toDateString(),
                'returned_date' => Carbon::now()->subDays(14)->toDateString(),
            ]),
            Borrowing::create([
                'user_id' => $student3->id,
                'item_id' => $createdItems[2]->id,
                'quantity' => 1,
                'status' => 'approved',
                'notes' => 'Mass measurement lab exercise',
                'requested_date' => Carbon::now()->subDays(1)->toDateString(),
                'approved_date' => Carbon::now()->toDateString(),
                'approved_by' => $staff->id,
                'expected_return_date' => Carbon::now()->addDays(5)->toDateString(),
            ]),
        ];

        $this->command->info('✓ Borrowings seeded (6 records - various statuses)');

        // ─── Reservations ──────────────────────────────────
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $rooms[0]->id,
            'reservation_type' => 'room',
            'start_datetime' => Carbon::now()->addDays(2)->setHour(9)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(2)->setHour(12)->setMinute(0),
            'purpose' => 'Biology group study session',
            'status' => 'approved',
            'approved_by' => $staff->id,
            'approved_at' => Carbon::now()->subHours(2),
        ]);

        Reservation::create([
            'user_id' => $student2->id,
            'item_id' => $createdItems[9]->id,
            'reservation_type' => 'equipment',
            'start_datetime' => Carbon::now()->addDays(3)->setHour(13)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(3)->setHour(16)->setMinute(0),
            'purpose' => 'Spectrophotometer readings for thesis',
            'status' => 'pending',
        ]);

        Reservation::create([
            'user_id' => $student3->id,
            'room_id' => $rooms[1]->id,
            'item_id' => $createdItems[8]->id,
            'reservation_type' => 'both',
            'start_datetime' => Carbon::now()->addDays(5)->setHour(8)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(5)->setHour(17)->setMinute(0),
            'purpose' => 'Full-day organic chemistry synthesis lab',
            'status' => 'pending',
        ]);

        $this->command->info('✓ Reservations seeded (3 records)');

        // ─── Maintenance Records ───────────────────────────
        MaintenanceRecord::create([
            'item_id' => $createdItems[4]->id, // Centrifuge - critical
            'maintenance_type' => 'corrective',
            'scheduled_date' => Carbon::now()->subDays(5)->toDateString(),
            'performed_by' => $staff->id,
            'condition_before' => 'Unusual vibration during operation',
            'wear_level' => 72,
            'issues_found' => 'Rotor bearing showing significant wear',
            'status' => 'in_progress',
            'notes' => 'Awaiting replacement bearing from supplier',
            'predictive_alert_sent' => true,
        ]);

        MaintenanceRecord::create([
            'item_id' => $createdItems[0]->id, // Microscope
            'maintenance_type' => 'preventive',
            'scheduled_date' => Carbon::now()->subDays(30)->toDateString(),
            'completed_date' => Carbon::now()->subDays(30)->toDateString(),
            'performed_by' => $staff->id,
            'condition_before' => 'Lens alignment slightly off',
            'condition_after' => 'Fully calibrated and cleaned',
            'wear_level' => 25,
            'actions_taken' => 'Cleaned lenses, realigned optics, updated firmware',
            'cost' => 2500.00,
            'next_maintenance_date' => Carbon::now()->addDays(60)->toDateString(),
            'status' => 'completed',
        ]);

        MaintenanceRecord::create([
            'item_id' => $createdItems[2]->id, // Lab Scale - upcoming
            'maintenance_type' => 'predictive',
            'scheduled_date' => Carbon::now()->addDays(10)->toDateString(),
            'condition_before' => 'Calibration drift detected',
            'wear_level' => 55,
            'status' => 'scheduled',
            'notes' => 'Auto-generated by predictive maintenance engine',
            'predictive_alert_sent' => true,
        ]);

        MaintenanceRecord::create([
            'item_id' => $createdItems[8]->id, // Fume Hood
            'maintenance_type' => 'routine',
            'scheduled_date' => Carbon::now()->subDays(45)->toDateString(),
            'completed_date' => Carbon::now()->subDays(45)->toDateString(),
            'performed_by' => $staff->id,
            'condition_before' => 'Filter nearing end of life',
            'condition_after' => 'New HEPA filter installed',
            'wear_level' => 30,
            'actions_taken' => 'Replaced HEPA filter, checked airflow velocity',
            'cost' => 8500.00,
            'next_maintenance_date' => Carbon::now()->addDays(45)->toDateString(),
            'status' => 'completed',
        ]);

        MaintenanceRecord::create([
            'item_id' => $createdItems[3]->id, // Bunsen Burner - overdue
            'maintenance_type' => 'routine',
            'scheduled_date' => Carbon::now()->subDays(3)->toDateString(),
            'wear_level' => 10,
            'status' => 'scheduled',
            'notes' => 'Routine gas line and nozzle check',
        ]);

        $this->command->info('✓ Maintenance records seeded (5 records)');

        // ─── Procurement Requests ──────────────────────────
        ProcurementRequest::create([
            'item_id' => $createdItems[4]->id, // Centrifuge bearing
            'supplier_id' => $suppliers[0]->id,
            'requested_by' => $staff->id,
            'quantity' => 1,
            'unit_price' => 15000.00,
            'total_price' => 15000.00,
            'justification' => 'Replacement bearing for centrifuge - critical maintenance',
            'urgency_level' => 'critical',
            'status' => 'ordered',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(3),
            'ordered_at' => Carbon::now()->subDays(2),
            'auto_generated' => false,
        ]);

        ProcurementRequest::create([
            'item_id' => $createdItems[6]->id, // Beakers - auto
            'supplier_id' => $suppliers[1]->id,
            'requested_by' => $admin->id,
            'quantity' => 20,
            'unit_price' => 150.00,
            'total_price' => 3000.00,
            'justification' => 'Stock approaching low threshold - auto-generated',
            'urgency_level' => 'medium',
            'status' => 'pending',
            'auto_generated' => true,
            'stock_threshold_triggered' => true,
        ]);

        ProcurementRequest::create([
            'item_id' => $createdItems[7]->id, // Safety Goggles
            'supplier_id' => $suppliers[1]->id,
            'requested_by' => $staff->id,
            'quantity' => 25,
            'unit_price' => 350.00,
            'total_price' => 8750.00,
            'justification' => 'Replenishment for upcoming semester lab sessions',
            'urgency_level' => 'low',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDay(),
            'auto_generated' => false,
        ]);

        ProcurementRequest::create([
            'item_id' => $createdItems[1]->id, // pH Meter
            'supplier_id' => $suppliers[1]->id,
            'requested_by' => $staff->id,
            'quantity' => 5,
            'unit_price' => 8500.00,
            'total_price' => 42500.00,
            'justification' => 'Additional units for expanded water quality program',
            'urgency_level' => 'medium',
            'status' => 'received',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(20),
            'ordered_at' => Carbon::now()->subDays(18),
            'received_at' => Carbon::now()->subDays(10),
            'auto_generated' => false,
        ]);

        $this->command->info('✓ Procurement requests seeded (4 records)');

        // ─── Notifications ─────────────────────────────────
        Notification::create([
            'user_id' => $student2->id,
            'type' => 'overdue_reminder',
            'title' => 'Overdue Return Reminder',
            'message' => 'Your borrowed pH Meter was due yesterday. Please return it as soon as possible.',
            'data' => ['borrowing_id' => $borrowings[1]->id],
            'priority' => 'high',
            'action_url' => '/dashboard',
        ]);

        Notification::create([
            'user_id' => $staff->id,
            'type' => 'maintenance_alert',
            'title' => 'Predictive Maintenance Alert',
            'message' => 'Centrifuge (INNO-EQ-005) has reached 72% wear. Maintenance recommended.',
            'data' => ['item_id' => $createdItems[4]->id],
            'priority' => 'high',
            'action_url' => '/maintenance/dashboard',
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'type' => 'low_stock_alert',
            'title' => 'Low Stock Alert',
            'message' => 'Auto-generated procurement request for Beaker Set - stock approaching threshold.',
            'data' => ['item_id' => $createdItems[6]->id],
            'priority' => 'medium',
            'action_url' => '/procurement/dashboard',
        ]);

        $this->command->info('✓ Notifications seeded (3 records)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  SEIMS Database Seeded Successfully');
        $this->command->info('  Login: admin@SEIMS.com / password123');
        $this->command->info('         staff@SEIMS.com / password123');
        $this->command->info('         student@SEIMS.com / password123');
        $this->command->info('═══════════════════════════════════════');
    }
}
