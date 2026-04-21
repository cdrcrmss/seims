<?php

namespace App\Models;

use App\Mail\LowStockAlert as LowStockAlertMail;
use App\Mail\MaintenanceAlert as MaintenanceAlertMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'action_url',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope by priority
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Create notification for low stock alert
     */
    public static function createLowStockAlert(Item $item, User $user)
    {
        $notification = self::create([
            'user_id' => $user->id,
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "Item '{$item->name}' is running low. Available: {$item->available_stock}, Threshold: {$item->low_stock_threshold}",
            'data' => [
                'item_id' => $item->id,
                'available_stock' => $item->available_stock,
                'threshold' => $item->low_stock_threshold,
            ],
            'action_url' => route('staff.items.edit', $item->id),
            'priority' => 'high',
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->queue(new LowStockAlertMail($item, $user));
        } catch (\Exception $e) {
            // Log the error but don't fail the notification creation
            \Log::error('Failed to send low stock email: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Create notification for maintenance due
     */
    public static function createMaintenanceAlert(Item $item, MaintenanceRecord $maintenance, User $user)
    {
        $notification = self::create([
            'user_id' => $user->id,
            'type' => 'maintenance_due',
            'title' => 'Maintenance Alert',
            'message' => "Maintenance required for '{$item->name}' scheduled on {$maintenance->scheduled_date->format('M d, Y')}",
            'data' => [
                'item_id' => $item->id,
                'maintenance_id' => $maintenance->id,
                'scheduled_date' => $maintenance->scheduled_date,
            ],
            'action_url' => route('staff.items.edit', $item->id),
            'priority' => 'high',
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->queue(new MaintenanceAlertMail($item, $maintenance, $user));
        } catch (\Exception $e) {
            // Log the error but don't fail the notification creation
            \Log::error('Failed to send maintenance email: ' . $e->getMessage());
        }

        return $notification;
    }
}
