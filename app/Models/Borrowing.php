<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

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
        'faculty_approved_by',
        'faculty_approved_date',
        'issued_date',
        'issued_by',
        'expected_return_date',
        'returned_date',
        'returned_to',
        'return_condition',
        'return_notes',
        'notes',
        'rejection_reason',
        'rejected_by',
        'rejected_date',
        'cancellation_reason',
        'extension_requested',
        'extension_date',
        'extension_reason',
        'extension_status',
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
            'faculty_approved_date' => 'datetime',
            'extension_requested' => 'boolean',
            'extension_date' => 'date',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
            'student_id' => null,
            'role' => 'unknown',
        ]);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault([
            'name' => 'Deleted Item',
            'image_path' => null,
        ]);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function returnedToUser()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function facultyApprover()
    {
        return $this->belongsTo(User::class, 'faculty_approved_by');
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