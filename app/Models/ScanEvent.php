<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanEvent extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'target_type',
        'target_id',
        'outcome',
        'outcome_message',
        'qr_code_value',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Action types
    const ACTION_BORROW_ISSUE = 'borrow_issue';
    const ACTION_BORROW_RETURN = 'borrow_return';
    const ACTION_ROOM_CHECK_IN = 'room_check_in';
    const ACTION_MAINTENANCE_START = 'maintenance_start';
    const ACTION_MAINTENANCE_COMPLETE = 'maintenance_complete';
    const ACTION_ITEM_LOOKUP = 'item_lookup';

    // Outcomes
    const OUTCOME_SUCCESS = 'success';
    const OUTCOME_WARNING = 'warning';
    const OUTCOME_BLOCKED = 'blocked';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->morphTo('target', 'target_type', 'target_id')->withDefault();
    }

    /**
     * Map short target_type strings to model classes for morphTo resolution.
     */
    public function getMorphClass()
    {
        return parent::getMorphClass();
    }

    /**
     * Register the morph map so 'item'/'room' strings resolve correctly.
     * Called from AppServiceProvider::boot().
     */
    public static function morphMap(): array
    {
        return [
            'item' => \App\Models\Item::class,
            'room' => \App\Models\Room::class,
        ];
    }

    public static function log(string $actionType, string $targetType, int $targetId, string $outcome, ?string $message = null, ?array $metadata = null, ?string $qrCode = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'outcome' => $outcome,
            'outcome_message' => $message,
            'qr_code_value' => $qrCode,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
