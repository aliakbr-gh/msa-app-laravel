<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function perPage(Request $request, int $default = 10): int
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->query('per_page', $default);

        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }

    protected function applyDateRange($query, Request $request, string $column = 'created_at')
    {
        if ($request->filled('date_from')) {
            $query->whereDate($column, '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate($column, '<=', $request->query('date_to'));
        }

        return $query;
    }

    protected function logActivity(string $action, ?string $module = null, ?string $description = null): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'username' => $user?->username,
            'role_name' => $user?->role?->name,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
