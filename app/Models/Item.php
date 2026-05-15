<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'laboratory',
        'asset_code',
        'qr_code',
        'asset_type',
        'is_perishable',
        'total_stock',
        'available_stock',
        'low_stock_threshold',
        'reorder_quantity',
        'unit_price',
        'image_path',
        'description',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_stock' => 'integer',
            'available_stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'reorder_quantity' => 'integer',
            'wear_level' => 'integer',
            'total_usage_count' => 'integer',
            'unit_price' => 'decimal:2',
            'is_perishable' => 'boolean',
            'last_maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
            'specifications' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function units()
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'item_supplier')
            ->withPivot('unit_price', 'is_preferred')
            ->withTimestamps();
    }

    public function preferredSupplier()
    {
        return $this->belongsToMany(Supplier::class, 'item_supplier')
            ->wherePivot('is_preferred', true)
            ->withPivot('unit_price')
            ->first();
    }

    /**
     * Get active borrowings for this item
     */
    public function activeBorrowings()
    {
        return $this->borrowings()->whereIn('status', ['pending', 'approved', 'issued']);
    }

    /**
     * Check if item is available for borrowing
     */
    public function isAvailable(int $quantity = 1): bool
    {
        return $this->available_stock >= $quantity
            && $this->status !== 'disposed';
    }

    /**
     * Recalculate stock from unit rows (disposed units are excluded).
     */
    public function syncStockFromUnits(): void
    {
        if ($this->status === 'disposed') {
            $this->update(['total_stock' => 0, 'available_stock' => 0]);

            return;
        }

        if (!$this->units()->exists()) {
            return;
        }

        $nonDisposed = $this->units()->where('status', '!=', 'disposed');
        $total = (clone $nonDisposed)->count();
        $available = (clone $nonDisposed)->where('status', 'available')->count();

        $this->update([
            'total_stock' => $total,
            'available_stock' => $available,
        ]);

        if ($total === 0 && $this->units()->where('status', 'disposed')->exists()) {
            $this->update(['status' => 'disposed']);
        }
    }

    /**
     * Mark entire item and all units as disposed (not borrowable).
     */
    public function applyDisposedState(): void
    {
        $this->units()->update([
            'status' => 'disposed',
            'current_borrower_id' => null,
            'borrowing_id' => null,
        ]);

        $this->update([
            'status' => 'disposed',
            'total_stock' => 0,
            'available_stock' => 0,
        ]);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->available_stock <= $this->low_stock_threshold;
    }

    /**
     * Check if maintenance is due soon (within 7 days)
     */
    public function isMaintenanceDue(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }

        return now()->diffInDays($this->next_maintenance_date, false) <= 7;
    }

    /**
     * Get condition status based on wear level
     */
    public function getConditionAttribute(): string
    {
        if ($this->wear_level >= 80) {
            return 'Critical';
        } elseif ($this->wear_level >= 60) {
            return 'Poor';
        } elseif ($this->wear_level >= 40) {
            return 'Fair';
        } elseif ($this->wear_level >= 20) {
            return 'Good';
        }

        return 'Excellent';
    }

    /**
     * Calculate utilization rate (%)
     */
    public function getUtilizationRateAttribute(): float
    {
        if ($this->total_stock === 0) {
            return 0.0;
        }

        $inUse = $this->total_stock - $this->available_stock;
        return ($inUse / $this->total_stock) * 100;
    }

    /**
     * Predict demand for next period
     */
    public function predictDemand(int $days = 30): float
    {
        $recentBorrowings = $this->borrowings()
            ->where('requested_date', '>=', now()->subDays($days))
            ->sum('quantity');

        return $recentBorrowings / max($days, 1);
    }

    /**
     * Generate QR code for the item
     */
    public function generateQRCode(): string
    {
        if (!$this->qr_code) {
            $this->qr_code = 'SEIMS-' . str_pad($this->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(8));
            $this->save();
        }

        return $this->qr_code;
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_stock', '<=', 'low_stock_threshold');
    }

    /**
     * Items students/staff can borrow (in stock, not disposed/retired).
     */
    public function scopeBorrowable($query)
    {
        return $query->where('available_stock', '>', 0)
            ->where(function ($q) {
                $q->whereNotIn('status', ['disposed', 'retired', 'maintenance', 'lost', 'damaged'])
                    ->orWhereNull('status');
            });
    }

    /**
     * Scope for maintenance due
     */
    public function scopeMaintenanceDue($query)
    {
        return $query->where('next_maintenance_date', '<=', now()->addDays(7))
            ->whereNotNull('next_maintenance_date');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_path 
            ? asset('storage/' . $this->image_path)
            : asset('images/default-item.png');
    }
}