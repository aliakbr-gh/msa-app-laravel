<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LedgerController extends Controller
{
    private array $modules = [
        'roti' => ['title' => 'Roti', 'has_qty' => false, 'qty_label' => null, 'rate_label' => null],
        'beef' => ['title' => 'Beef', 'has_qty' => true, 'qty_label' => 'Qty (Kg)', 'rate_label' => 'Beef Rate'],
        'chicken-1' => ['title' => 'Chicken 1', 'has_qty' => true, 'qty_label' => 'Weight (Kg)', 'rate_label' => 'Farm Rate'],
        'chicken-2' => ['title' => 'Chicken 2', 'has_qty' => true, 'qty_label' => 'Weight (Kg)', 'rate_label' => 'Farm Rate'],
    ];

    public function index(string $module)
    {
        $config = $this->config($module);
        $entries = LedgerEntry::where('module', $module)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return response()->view('ledger.index', compact('entries', 'module', 'config'));
    }

    public function create(string $module)
    {
        $config = $this->config($module);
        $lastEntry = LedgerEntry::where('module', $module)->latest('id')->first();

        return response()->view('ledger.create', compact('module', 'config', 'lastEntry'));
    }

    public function store(Request $request, string $module)
    {
        $this->config($module);
        $data = $this->validatedData($request, $module);
        $data['module'] = $module;

        $entry = LedgerEntry::create($data);
        $this->recalculateBalances($module);

        return APIResponse::success('Record created successfully', $entry->fresh(), 201);
    }

    public function edit(string $module, LedgerEntry $entry)
    {
        $config = $this->config($module);
        $this->ensureModuleEntry($module, $entry);

        return response()->view('ledger.edit', compact('module', 'config', 'entry'));
    }

    public function update(Request $request, string $module, LedgerEntry $entry)
    {
        $this->config($module);
        $this->ensureModuleEntry($module, $entry);

        $entry->update($this->validatedData($request, $module));
        $this->recalculateBalances($module);

        return APIResponse::success('Record updated successfully', $entry->fresh());
    }

    public function destroy(string $module, LedgerEntry $entry)
    {
        $this->config($module);
        $this->ensureModuleEntry($module, $entry);
        $entry->delete();
        $this->recalculateBalances($module);

        return APIResponse::success('Record deleted successfully');
    }

    private function validatedData(Request $request, string $module): array
    {
        $rules = [
            'entry_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
        ];

        if ($this->modules[$module]['has_qty']) {
            $rules['qty'] = 'required|numeric|min:0';
            $rules['rate'] = 'required|numeric|min:0';
        }

        return $request->validate($rules);
    }

    private function recalculateBalances(string $module): void
    {
        $entries = LedgerEntry::where('module', $module)
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        $balance = 0;

        foreach ($entries as $index => $entry) {
            if ($index === 0 && $entry->opening_balance !== null) {
                $balance = (float) $entry->opening_balance;
            }

            $amount = (float) $entry->amount;
            $balance = $entry->isPaid() ? $balance - $amount : $balance + $amount;
            $entry->remaining_balance = $balance;
            $entry->save();
        }
    }

    private function config(string $module): array
    {
        abort_unless(array_key_exists($module, $this->modules), 404);

        return $this->modules[$module];
    }

    private function ensureModuleEntry(string $module, LedgerEntry $entry): void
    {
        abort_unless($entry->module === $module, 404);
    }
}
