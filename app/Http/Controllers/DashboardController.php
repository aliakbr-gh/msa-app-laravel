<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeSalaryPayment;
use App\Models\KitchenRecord;
use App\Models\LedgerEntry;
use App\Models\OrderRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $todayOrders = OrderRecord::whereDate('order_date', $today)->get();
        $monthOrders = OrderRecord::whereBetween('order_date', [$monthStart, $monthEnd])->get();
        $todayAttendance = EmployeeAttendance::whereDate('attendance_date', $today)->get();
        $monthSalaryPaid = EmployeeSalaryPayment::whereBetween('paid_on', [$monthStart, $monthEnd])->sum('amount');
        $monthLedgers = LedgerEntry::whereBetween('entry_date', [$monthStart, $monthEnd])->get();

        $summary = [
            'employees' => Employee::count(),
            'today_orders' => $todayOrders->count(),
            'today_sales' => $todayOrders->sum('total_amount'),
            'month_orders' => $monthOrders->count(),
            'month_sales' => $monthOrders->sum('total_amount'),
            'pending_payment' => OrderRecord::sum('pending_payment'),
            'today_present' => $todayAttendance->where('status', 'present')->count(),
            'today_absent' => $todayAttendance->where('status', 'absent')->count(),
            'salary_paid' => $monthSalaryPaid,
            'food_purchases' => $monthLedgers->reject->isPaid()->sum('amount'),
            'food_paid' => $monthLedgers->filter->isPaid()->sum('amount'),
            'kitchen_records' => KitchenRecord::whereDate('created_at', $today)->count(),
        ];

        $foodBalances = collect([
            'roti' => 'Roti',
            'beef' => 'Beef',
            'chicken-1' => 'Chicken 1',
            'chicken-2' => 'Chicken 2',
        ])->map(function ($title, $module) {
            $entry = LedgerEntry::where('module', $module)->latest('entry_date')->latest('id')->first();

            return [
                'title' => $title,
                'balance' => $entry?->remaining_balance ?? 0,
            ];
        })->values();

        $charts = [
            'orders' => [
                'labels' => ['Today', 'This Month'],
                'data' => [$summary['today_sales'], $summary['month_sales']],
            ],
            'attendance' => [
                'labels' => ['Present', 'Absent'],
                'data' => [$summary['today_present'], $summary['today_absent']],
            ],
            'foodBalances' => [
                'labels' => $foodBalances->pluck('title'),
                'data' => $foodBalances->pluck('balance'),
            ],
            'cash' => [
                'labels' => ['Month Sales', 'Pending Payment', 'Salary Paid', 'Food Purchases'],
                'data' => [
                    $summary['month_sales'],
                    $summary['pending_payment'],
                    $summary['salary_paid'],
                    $summary['food_purchases'],
                ],
            ],
        ];

        $recentOrders = OrderRecord::latest('order_date')->latest('id')->take(5)->get();
        $recentLedgers = LedgerEntry::latest('entry_date')->latest('id')->take(5)->get();

        return response()->view('dashboard.dashboard', compact(
            'summary',
            'foodBalances',
            'charts',
            'recentOrders',
            'recentLedgers'
        ));
    }
}
