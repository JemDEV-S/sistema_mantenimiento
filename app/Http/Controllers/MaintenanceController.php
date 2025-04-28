<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::with(['asset', 'technician']);
        
        // Filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        
        if ($request->has('technician_id') && $request->technician_id != '') {
            $query->where('technician_id', $request->technician_id);
        }
        
        $maintenances = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('maintenances.index', compact('maintenances'));
    }
    
    public function create($asset_id = null)
    {
        $asset = null;
        $assets = Asset::all();
        
        if ($asset_id) {
            $asset = Asset::findOrFail($asset_id);
        }
        
        return view('maintenances.create', compact('asset', 'assets'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_type' => 'required|string|in:preventive,corrective,predictive',
            'diagnosis' => 'required|string',
            'procedure' => 'required|string',
        ]);
        
        $maintenance = Maintenance::create([
            'asset_id' => $validated['asset_id'],
            'technician_id' => Auth::id(),
            'maintenance_type' => $validated['maintenance_type'],
            'diagnosis' => $validated['diagnosis'],
            'procedure' => $validated['procedure'],
            'status' => 'in_progress',
            'start_date' => Carbon::now(),
        ]);
        
        // Update asset status
        $asset = Asset::find($validated['asset_id']);
        $asset->update(['status' => 'in_maintenance']);
        
        return redirect()->route('maintenances.show', $maintenance->id)
            ->with('success', 'Mantenimiento iniciado correctamente');
    }
    
    public function show($id)
    {
        $maintenance = Maintenance::with(['asset.department', 'technician', 'certificate'])->findOrFail($id);
        
        return view('maintenances.show', compact('maintenance'));
    }
    
    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        if ($maintenance->status === 'completed') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'No se puede editar un mantenimiento completado');
        }
        
        return view('maintenances.edit', compact('maintenance'));
    }
    
    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        if ($maintenance->status === 'completed') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'No se puede editar un mantenimiento completado');
        }
        
        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'procedure' => 'required|string',
            'status' => 'required|in:in_progress,completed',
        ]);
        
        $maintenance->update($validated);
        
        if ($validated['status'] === 'completed') {
            $maintenance->update(['end_date' => Carbon::now()]);
            
            // Update asset status
            $asset = $maintenance->asset;
            $asset->update(['status' => 'active']);
        }
        
        return redirect()->route('maintenances.show', $maintenance->id)
            ->with('success', 'Mantenimiento actualizado correctamente');
    }
    
    public function complete($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        if ($maintenance->status === 'completed') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'El mantenimiento ya está completado');
        }
        
        $maintenance->update([
            'status' => 'completed',
            'end_date' => Carbon::now(),
        ]);
        
        // Update asset status
        $asset = $maintenance->asset;
        $asset->update(['status' => 'active']);
        
        return redirect()->route('maintenances.show', $maintenance->id)
            ->with('success', 'Mantenimiento completado correctamente');
    }
}
