<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log specific actions
        if ($this->shouldLog($request)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged
     */
    private function shouldLog(Request $request): bool
    {
        // Don't log GET requests unless they're specific read operations
        if ($request->isMethod('GET') && !$this->isImportantGetRequest($request)) {
            return false;
        }

        // Don't log OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            return false;
        }

        // Don't log asset requests
        if ($request->is('storage/*') || $request->is('images/*') || $request->is('css/*') || $request->is('js/*')) {
            return false;
        }

        return true;
    }

    /**
     * Check if this is an important GET request to log
     */
    private function isImportantGetRequest(Request $request): bool
    {
        $importantPaths = [
            'admin/reports',
            'staff/reports',
            'admin/users/*/edit',
            'staff/items/*/edit',
        ];

        foreach ($importantPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log the request
     */
    private function logRequest(Request $request, Response $response): void
    {
        $user = auth()->user();
        $action = $this->determineAction($request);
        $module = $this->determineModule($request);
        $severity = $this->determineSeverity($request, $response);

        try {
            AuditLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'auditable_type' => $this->getModelType($request),
                'auditable_id' => $this->getModelId($request),
                'old_values' => [],
                'new_values' => $this->getSanitizedInput($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'module' => $module,
                'severity' => $severity,
                'description' => $this->generateDescription($user, $action, $module),
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent disrupting the application
            \Log::error('Failed to create audit log: ' . $e->getMessage());
        }
    }

    /**
     * Determine the action from the request
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $route = $request->route();

        if (!$route) {
            return strtolower($method);
        }

        $action = $route->getActionMethod();

        // Map common action names
        $actionMap = [
            'store' => 'create',
            'update' => 'update',
            'destroy' => 'delete',
            'show' => 'view',
            'index' => 'list',
            'approve' => 'approve',
            'reject' => 'reject',
            'markNoShow' => 'no_show',
            'complete' => 'complete',
            'cancel' => 'cancel',
            'issue' => 'issue',
            'return' => 'return',
        ];

        return $actionMap[$action] ?? $action;
    }

    /**
     * Determine the module from the request path
     */
    private function determineModule(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'admin')) {
            return 'admin';
        } elseif (str_contains($path, 'staff')) {
            return 'staff';
        } elseif (str_contains($path, 'student')) {
            return 'student';
        } elseif (str_contains($path, 'borrowing')) {
            return 'borrowing';
        } elseif (str_contains($path, 'reservation')) {
            return 'reservation';
        } elseif (str_contains($path, 'maintenance')) {
            return 'maintenance';
        } elseif (str_contains($path, 'procurement')) {
            return 'procurement';
        } elseif (str_contains($path, 'item')) {
            return 'inventory';
        }

        return 'system';
    }

    /**
     * Determine severity level
     */
    private function determineSeverity(Request $request, Response $response): string
    {
        $statusCode = $response->getStatusCode();

        // Errors are critical
        if ($statusCode >= 500) {
            return 'critical';
        }

        if ($statusCode >= 400) {
            return 'warning';
        }

        // Destructive actions are warnings
        if ($request->isMethod('DELETE')) {
            return 'warning';
        }

        // Approval/rejection actions
        $action = $this->determineAction($request);
        if (in_array($action, ['approve', 'reject', 'delete', 'no_show', 'complete', 'cancel'])) {
            return 'warning';
        }

        return 'info';
    }

    /**
     * Get the model type from the request
     */
    private function getModelType(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $parameters = $route->parameters();
        
        if (isset($parameters['item'])) {
            return 'App\Models\Item';
        } elseif (isset($parameters['borrowing'])) {
            return 'App\Models\Borrowing';
        } elseif (isset($parameters['reservation'])) {
            return 'App\Models\Reservation';
        } elseif (isset($parameters['user'])) {
            return 'App\Models\User';
        } elseif (isset($parameters['maintenance'])) {
            return 'App\Models\MaintenanceRecord';
        } elseif (isset($parameters['procurementRequest'])) {
            return 'App\Models\ProcurementRequest';
        } elseif (isset($parameters['procurement'])) {
            return 'App\Models\ProcurementRequest';
        }

        return null;
    }

    /**
     * Get the model ID from the request
     */
    private function getModelId(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $parameters = $route->parameters();
        
        foreach ($parameters as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                return $parameter->getKey();
            }
        }

        return null;
    }

    /**
     * Get sanitized input (remove sensitive data)
     */
    private function getSanitizedInput(Request $request): array
    {
        $input = $request->except([
            'password',
            'password_confirmation',
            'current_password',
            '_token',
            '_method',
        ]);

        return $input;
    }

    /**
     * Generate human-readable description
     */
    private function generateDescription($user, string $action, string $module): string
    {
        $userName = $user ? $user->name : 'Anonymous';
        $userRole = $user ? ucfirst($user->role) : 'Guest';

        return "{$userName} ({$userRole}) performed {$action} in {$module} module";
    }
}
