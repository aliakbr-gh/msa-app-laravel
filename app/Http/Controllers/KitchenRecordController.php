<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\KitchenRecord;
use Illuminate\Http\Request;

class KitchenRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = KitchenRecord::latest()->paginate($this->perPage($request))->withQueryString();

        return response()->view('kitchen.index', compact('records'));
    }

    public function create()
    {
        return response()->view('kitchen.create');
    }

    public function store(Request $request)
    {
        $record = KitchenRecord::create($this->validatedData($request));

        return APIResponse::success('Kitchen record created successfully', $record, 201);
    }

    public function edit(KitchenRecord $record)
    {
        return response()->view('kitchen.edit', compact('record'));
    }

    public function update(Request $request, KitchenRecord $record)
    {
        $record->update($this->validatedData($request));

        return APIResponse::success('Kitchen record updated successfully', $record);
    }

    public function destroy(KitchenRecord $record)
    {
        $record->delete();

        return APIResponse::success('Kitchen record deleted successfully');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'details' => 'required|string',
            'order_shop' => 'required|string|max:255',
            'qty' => 'required|numeric|min:0',
        ]);
    }
}
