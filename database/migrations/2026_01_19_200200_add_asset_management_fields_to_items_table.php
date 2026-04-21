<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('asset_code')->unique()->nullable()->after('id');
            $table->string('qr_code')->unique()->nullable()->after('asset_code');
            $table->enum('asset_type', ['equipment', 'hospitality_supply', 'consumable', 'furniture', 'electronics'])->default('equipment')->after('category');
            $table->boolean('is_perishable')->default(false)->after('asset_type');
            $table->decimal('unit_price', 10, 2)->nullable()->after('description');
            $table->integer('low_stock_threshold')->default(5)->after('available_stock');
            $table->integer('reorder_quantity')->default(10)->after('low_stock_threshold');
            $table->integer('wear_level')->default(0)->after('reorder_quantity')->comment('0-100 scale for condition');
            $table->date('last_maintenance_date')->nullable()->after('wear_level');
            $table->date('next_maintenance_date')->nullable()->after('last_maintenance_date');
            $table->integer('total_usage_count')->default(0)->after('next_maintenance_date');
            $table->string('location')->nullable()->after('total_usage_count');
            $table->string('barcode')->nullable()->after('location');
            $table->json('specifications')->nullable()->after('barcode');
            $table->date('purchase_date')->nullable()->after('specifications');
            $table->date('warranty_expiry')->nullable()->after('purchase_date');
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired', 'damaged'])->default('available')->after('warranty_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'asset_code',
                'qr_code',
                'asset_type',
                'is_perishable',
                'unit_price',
                'low_stock_threshold',
                'reorder_quantity',
                'wear_level',
                'last_maintenance_date',
                'next_maintenance_date',
                'total_usage_count',
                'location',
                'barcode',
                'specifications',
                'purchase_date',
                'warranty_expiry',
                'status',
            ]);
        });
    }
};
