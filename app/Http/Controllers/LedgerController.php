<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\LedgerEntry;
use App\Models\LedgerModule;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request, string $module)
    {
        $config = $this->config($module);
        $entries = $this->applyDateRange(LedgerEntry::where('module', $module), $request, 'entry_date')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate($this->perPage($request))
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
        $this->logActivity('create', $module, "Created ledger record");

        return APIResponse::success('Record created successfully', $entry->fresh(), 201);
    }

    public function pay(Request $request, string $module)
    {
        $this->config($module);
        $lastEntry = LedgerEntry::where('module', $module)->latest('id')->first();
        $data = $request->validate([
            'entry_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $entry = LedgerEntry::create([
            'module' => $module,
            'entry_date' => $data['entry_date'],
            'description' => 'Paid',
            'amount' => $data['amount'],
            'opening_balance' => $lastEntry ? null : 0,
        ]);
        $this->recalculateBalances($module);
        $this->logActivity('pay', $module, "Recorded payment in {$module}");

        return APIResponse::success('Payment recorded successfully', $entry->fresh(), 201);
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
        $this->logActivity('update', $module, "Updated ledger record #{$entry->id}");

        return APIResponse::success('Record updated successfully', $entry->fresh());
    }

    public function destroy(string $module, LedgerEntry $entry)
    {
        $this->config($module);
        $this->ensureModuleEntry($module, $entry);
        $entry->delete();
        $this->recalculateBalances($module);
        $this->logActivity('delete', $module, "Deleted ledger record #{$entry->id}");

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

        if ($this->config($module)['has_qty']) {
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
        $ledgerModule = LedgerModule::where('slug', $module)->where('is_active', true)->first();
        abort_unless($ledgerModule, 404);

        return [
            'title' => $ledgerModule->title,
            'has_qty' => $ledgerModule->has_qty,
            'qty_label' => $ledgerModule->qty_label,
            'rate_label' => $ledgerModule->rate_label,
        ];
    }

    private function ensureModuleEntry(string $module, LedgerEntry $entry): void
    {
        abort_unless($entry->module === $module, 404);
    }
}
