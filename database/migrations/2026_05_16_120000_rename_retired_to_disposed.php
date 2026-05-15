<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Add 'disposed' to enums before updating rows (retired still valid).
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'retired', 'disposed', 'damaged', 'lost') DEFAULT 'available'");
            DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'damaged', 'needs_repair', 'lost', 'retired', 'disposed') DEFAULT 'available'");
        }

        DB::table('items')->where('status', 'retired')->update(['status' => 'disposed']);
        DB::table('item_units')->where('status', 'retired')->update(['status' => 'disposed']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'disposed', 'damaged', 'lost') DEFAULT 'available'");
            DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'damaged', 'needs_repair', 'lost', 'disposed') DEFAULT 'available'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'retired', 'disposed', 'damaged', 'lost') DEFAULT 'available'");
            DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'damaged', 'needs_repair', 'lost', 'retired', 'disposed') DEFAULT 'available'");
        }

        DB::table('items')->where('status', 'disposed')->update(['status' => 'retired']);
        DB::table('item_units')->where('status', 'disposed')->update(['status' => 'retired']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'retired', 'damaged', 'lost') DEFAULT 'available'");
            DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'damaged', 'needs_repair', 'lost', 'retired') DEFAULT 'available'");
        }
    }
};
