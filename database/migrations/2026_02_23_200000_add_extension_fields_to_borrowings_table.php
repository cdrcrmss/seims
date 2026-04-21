<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->boolean('extension_requested')->default(false)->after('return_notes');
            $table->date('extension_date')->nullable()->after('extension_requested');
            $table->text('extension_reason')->nullable()->after('extension_date');
            $table->string('extension_status', 20)->nullable()->after('extension_reason'); // pending, approved, rejected
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['extension_requested', 'extension_date', 'extension_reason', 'extension_status']);
        });
    }
};
