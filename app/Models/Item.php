<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'total_stock',
        'available_stock',
        'image_path',
        'description',
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
        ];
    }

    /**
     * Relationships
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
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
        return $this->available_stock >= $quantity;
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