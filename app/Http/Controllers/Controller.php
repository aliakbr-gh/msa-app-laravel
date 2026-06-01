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
}
