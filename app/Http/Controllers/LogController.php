<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = $this->applyDateRange(ActivityLog::with('user')->latest(), $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->view('logs.index', compact('logs'));
    }
}
