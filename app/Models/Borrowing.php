<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'quantity',
        'requested_date',
        'approved_date',
        'approved_by',
        'issued_date',
        'issued_by',
        'expected_return_date',
        'returned_date',
        'notes',
        'rejection_reason',
        'rejected_by',
        'rejected_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'requested_date' => 'date',
            'approved_date' => 'date',
            'issued_date' => 'date',
            'expected_return_date' => 'date',
            'returned_date' => 'date',
            'rejected_date' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Status checking methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    /**
     * Check if the borrowing is overdue
     */
    public function isOverdue(): bool
    {
        return $this->isIssued() && 
               $this->expected_return_date && 
               $this->expected_return_date < now();
    }
}