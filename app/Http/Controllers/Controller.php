<?php

namespace App\Http\Controllers;

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
}
