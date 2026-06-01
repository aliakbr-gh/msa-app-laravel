<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryPayment extends Model
{
    protected $fillable = [
        'employee_id',
        'paid_on',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
