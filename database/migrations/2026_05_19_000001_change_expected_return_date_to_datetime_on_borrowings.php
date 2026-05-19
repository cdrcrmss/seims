<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dateTime('expected_return_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->date('expected_return_date')->nullable()->change();
        });
    }
};
