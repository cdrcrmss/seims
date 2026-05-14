<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'damaged', 'needs_repair', 'lost', 'retired') DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE item_units MODIFY COLUMN status ENUM('available', 'borrowed', 'maintenance', 'lost', 'retired') DEFAULT 'available'");
    }
};
