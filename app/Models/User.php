<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'role',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Check if the user account is approved.
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Role checking methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isFaculty(): bool
    {
        return $this->role === 'faculty';
    }

    /**
     * Check if user can approve faculty-level requests
     */
    public function canApproveFacultyLevel(): bool
    {
        return in_array($this->role, ['faculty', 'staff', 'admin']);
    }

    /**
     * Check if user can approve staff-level requests (final approval)
     */
    public function canApproveStaffLevel(): bool
    {
        return in_array($this->role, ['staff', 'admin']);
    }

    /**
     * Relationships
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get active borrowings
     */
    public function activeBorrowings()
    {
        return $this->borrowings()->whereIn('status', ['pending', 'approved', 'issued']);
    }
}
