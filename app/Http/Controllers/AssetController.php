<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Department;
use App\Services\OcsInventoryService;

class AssetController extends Controller
{
    protected $ocsService;
    
    public function __construct(OcsInventoryService $ocsService)
    {
        $this->ocsService = $ocsService;
    }
    
    public function index(Request $request)
    {
        $query = Asset::with('department');
        
        // Filters
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('serial', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
                  ->orWhere('model', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }
        
        $assets = $query->paginate(10);
        $departments = Department::all();
        $assetTypes = Asset::select('type')->distinct()->pluck('type');
        
        return view('assets.index', compact('assets', 'departments', 'assetTypes'));
    }
    
    public function show($id)
    {
        $asset = Asset::with(['department', 'maintenances.technician'])->findOrFail($id);
        
        // Get additional details from OCS Inventory
        $ocsDetails = $this->ocsService->getEquipmentDetails($asset->ocs_id);
        
        return view('assets.show', compact('asset', 'ocsDetails'));
    }
    
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $departments = Department::all();
        
        return view('assets.edit', compact('asset', 'departments'));
    }
    
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|string|in:active,inactive,in_maintenance,decommissioned',
            'patrimony_code' => 'nullable|string|max:50',
        ]);
        
        $asset->update($validated);
        
        return redirect()->route('assets.show', $asset->id)
            ->with('success', 'Activo actualizado correctamente');
    }
    
    public function sync()
    {
        try {
            $newAssets = $this->ocsService->syncAssets();
            return redirect()->route('assets.index')
                ->with('success', "Sincronización completada. {$newAssets} nuevos activos importados.");
        } catch (\Exception $e) {
            return redirect()->route('assets.index')
                ->with('error', 'Error durante la sincronización: ' . $e->getMessage());
        }
    }
}
