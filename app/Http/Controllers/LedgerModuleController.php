<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\LedgerModule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LedgerModuleController extends Controller
{
    public function index(Request $request)
    {
        $modules = $this->applyDateRange(LedgerModule::latest(), $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->view('modules.index', compact('modules'));
    }

    public function create()
    {
        return response()->view('modules.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = LedgerModule::slugFromTitle($data['title']);
        $data['has_qty'] = (bool) ($data['has_qty'] ?? false);
        $data['is_active'] = 1;

        $module = LedgerModule::create($data);

        return APIResponse::success('Module created successfully', $module, 201);
    }

    public function edit(LedgerModule $module)
    {
        return response()->view('modules.edit', compact('module'));
    }

    public function update(Request $request, LedgerModule $module)
    {
        $data = $this->validatedData($request, $module->id);
        $data['slug'] = LedgerModule::slugFromTitle($data['title']);
        $data['has_qty'] = (bool) ($data['has_qty'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $module->update($data);

        return APIResponse::success('Module updated successfully', $module->fresh());
    }

    public function destroy(LedgerModule $module)
    {
        $module->delete();

        return APIResponse::success('Module deleted successfully');
    }

    private function validatedData(Request $request, ?int $moduleId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('ledger_modules', 'title')->ignore($moduleId)],
            'has_qty' => 'nullable|boolean',
            'qty_label' => 'nullable|string|max:255',
            'rate_label' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
