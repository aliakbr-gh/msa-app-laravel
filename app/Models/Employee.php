<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'salary',
        'cnic',
        'picture',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class);
    }
}
