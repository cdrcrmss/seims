<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds:
     * 1. Support for 'faculty' role (multi-level approval)
     * 2. Faculty approval fields to borrowings
     * 3. Return condition field to borrowings
     * 4. 'lost' status to items
     */
    public function up(): void
    {
        // 1. Add faculty approval fields and return condition to borrowings
        Schema::table('borrowings', function (Blueprint $table) {
            // Faculty approval for multi-level workflow
            $table->foreignId('faculty_approved_by')->nullable()->after('approved_by')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('faculty_approved_date')->nullable()->after('faculty_approved_by');
            
            // Condition reporting on return
            $table->enum('return_condition', ['good', 'fair', 'needs_repair', 'damaged'])->nullable()
                  ->after('returned_to');
            $table->text('return_notes')->nullable()->after('return_condition');
        });

        // 2. Update items status enum to include 'lost'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'retired', 'damaged', 'lost') DEFAULT 'available'");
        } else {
            // SQLite does not support enum; column is already a string type
            Schema::table('items', function (Blueprint $table) {
                $table->string('status')->default('available')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['faculty_approved_by']);
            $table->dropColumn(['faculty_approved_by', 'faculty_approved_date', 'return_condition', 'return_notes']);
        });

        // Revert items status enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'retired', 'damaged') DEFAULT 'available'");
        }
    }
};
