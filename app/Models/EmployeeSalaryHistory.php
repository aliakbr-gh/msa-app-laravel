<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'salary',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }
}
