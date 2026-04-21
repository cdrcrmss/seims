<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'payment_terms',
        'delivery_time_days',
        'rating',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_time_days' => 'integer',
            'rating' => 'decimal:1',
        ];
    }

    /**
     * Relationships
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_supplier')
            ->withPivot('unit_price', 'is_preferred')
            ->withTimestamps();
    }

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }

    /**
     * Scope for active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get average delivery performance
     */
    public function averageDeliveryDays()
    {
        return $this->procurementRequests()
            ->whereNotNull('ordered_at')
            ->whereNotNull('received_at')
            ->get()
            ->avg(function ($request) {
                return $request->ordered_at->diffInDays($request->received_at);
            });
    }
}
