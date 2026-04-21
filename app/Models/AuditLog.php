<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'module',
        'severity',
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
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Log an action
     */
    public static function logAction(string $action, $model, array $oldValues = [], array $newValues = [], string $module = 'system', string $severity = 'info')
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id ?? null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'module' => $module,
            'severity' => $severity,
            'description' => self::generateDescription($action, $model),
        ]);
    }

    /**
     * Generate human-readable description
     */
    private static function generateDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $modelId = $model->id ?? 'new';
        $userName = auth()->user()->name ?? 'System';

        return "{$userName} performed {$action} on {$modelName} (ID: {$modelId})";
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope by module
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope by severity
     */
    public function scopeSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }
}
