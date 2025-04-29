<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MaintenanceFlowController extends Controller
{
    /**
     * Muestra la vista inicial de selección de activo
     */
    public function selectAsset(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $assetTypes = Asset::select('type')->distinct()->pluck('type');
        $recentAssets = Auth::user()->recentAssets ?? 
                        Asset::orderBy('updated_at', 'desc')->take(5)->get();
        
        return view('maintenance-flow.select-asset', compact(
            'departments', 
            'assetTypes', 
            'recentAssets'
        ));
    }
    
    /**
     * Busca activos según los criterios de filtro (para AJAX)
     */
    public function searchAssets(Request $request)
    {
        $query = Asset::with('department');
        
        // Aplicar filtros
        if ($request->has('q') && !empty($request->q)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('serial', 'like', "%{$request->q}%")
                  ->orWhere('patrimony_code', 'like', "%{$request->q}%");
            });
        }
        
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Limitar resultados para mejor rendimiento
        $assets = $query->take(20)->get();
        
        return response()->json($assets);
    }
    
    /**
     * Busca un activo por código QR/barras
     */
    public function findByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Código inválido'], 422);
        }
        
        $code = $request->code;
        
        // Buscar por diferentes campos donde podría estar el código
        $asset = Asset::where('patrimony_code', $code)
                      ->orWhere('serial', $code)
                      ->orWhere('ocs_id', $code)
                      ->first();
        
        if (!$asset) {
            return response()->json(['error' => 'Activo no encontrado'], 404);
        }
        
        return response()->json($asset);
    }
    
    /**
     * Muestra el formulario para registrar un nuevo activo
     */
    public function createAsset()
    {
        $departments = Department::orderBy('name')->get();
        return view('maintenance-flow.create-asset', compact('departments'));
    }
    
    /**
     * Guarda un nuevo activo en la base de datos
     */
    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial' => 'required|string|max:100|unique:assets,serial',
            'patrimony_code' => 'nullable|string|max:50|unique:assets,patrimony_code',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        // Generar un OCS ID temporal
        $validated['ocs_id'] = 'TEMP-' . Str::random(8);
        $validated['status'] = 'active';
        $validated['last_sync'] = Carbon::now();
        
        $asset = Asset::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Activo creado correctamente',
                'asset' => $asset
            ]);
        }
        
        return redirect()->route('maintenance-flow.maintenance-form', $asset->id)
            ->with('success', 'Activo creado correctamente');
    }
    
    /**
     * Muestra el formulario de mantenimiento por pasos
     */
    public function maintenanceForm($asset_id)
    {
        $asset = Asset::with('department')->findOrFail($asset_id);
        
        // Comprobar si el activo ya está en mantenimiento
        if ($asset->status === 'in_maintenance') {
            $existingMaintenance = Maintenance::where('asset_id', $asset->id)
                                            ->where('status', 'in_progress')
                                            ->first();
            
            if ($existingMaintenance) {
                return redirect()->route('maintenance-flow.maintenance-form-edit', $existingMaintenance->id)
                    ->with('info', 'Este activo ya tiene un mantenimiento en progreso');
            }
        }
        
        // Obtener mantenimientos anteriores para referencia
        $previousMaintenances = Maintenance::where('asset_id', $asset->id)
                                         ->where('status', 'completed')
                                         ->orderBy('end_date', 'desc')
                                         ->take(3)
                                         ->get();
        
        return view('maintenance-flow.maintenance-form', compact(
            'asset', 
            'previousMaintenances'
        ));
    }
    
    /**
     * Guarda el nuevo mantenimiento
     */
    public function storeMaintenance(Request $request, $asset_id)
    {
        $asset = Asset::findOrFail($asset_id);
        
        $validated = $request->validate([
            'maintenance_type' => 'required|string|in:preventive,corrective,predictive',
            'priority' => 'required|string|in:low,medium,high,critical',
            'estimated_time' => 'nullable|integer',
            'diagnosis' => 'required|string',
            'procedure' => 'required|string',
            'materials_used' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120', // Máximo 5MB por imagen
        ]);
        
        // Iniciar transacción para asegurar la integridad de los datos
        \DB::beginTransaction();
        
        try {
            // Crear el mantenimiento
            $maintenance = Maintenance::create([
                'asset_id' => $asset->id,
                'technician_id' => Auth::id(),
                'maintenance_type' => $validated['maintenance_type'],
                'diagnosis' => $validated['diagnosis'],
                'procedure' => $validated['procedure'],
                'status' => $request->has('complete_now') ? 'completed' : 'in_progress',
                'start_date' => Carbon::now(),
                'end_date' => $request->has('complete_now') ? Carbon::now() : null,
                // Guardamos campos adicionales en un JSON
                'additional_info' => json_encode([
                    'priority' => $validated['priority'],
                    'estimated_time' => $validated['estimated_time'],
                    'materials_used' => $validated['materials_used'],
                    'recommendations' => $validated['recommendations'],
                ]),
            ]);
            
            // Actualizar el estado del activo
            $asset->update([
                'status' => $request->has('complete_now') ? 'active' : 'in_maintenance'
            ]);
            
            // Procesar imágenes si se subieron
            if ($request->hasFile('images')) {
                $this->processMaintenanceImages($request->file('images'), $maintenance);
            }
            
            \DB::commit();
            
            // Si se ha completado, redirigir a generación de acta
            if ($request->has('complete_now')) {
                return redirect()->route('certificates.create', $maintenance->id)
                    ->with('success', 'Mantenimiento registrado como completado');
            }
            
            return redirect()->route('maintenance-flow.confirmation', $maintenance->id)
                ->with('success', 'Mantenimiento iniciado correctamente');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al guardar el mantenimiento: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el formulario para editar un mantenimiento en progreso
     */
    public function maintenanceFormEdit($maintenance_id)
    {
        $maintenance = Maintenance::with(['asset.department'])->findOrFail($maintenance_id);
        
        // Verificar que el mantenimiento esté en progreso
        if ($maintenance->status !== 'in_progress') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'Solo se pueden editar mantenimientos en progreso');
        }
        
        // Decodificar información adicional
        $additionalInfo = json_decode($maintenance->additional_info ?? '{}', true);
        
        return view('maintenance-flow.maintenance-form-edit', compact('maintenance', 'additionalInfo'));
    }
    
    /**
     * Actualiza un mantenimiento en progreso
     */
    public function updateMaintenance(Request $request, $maintenance_id)
    {
        $maintenance = Maintenance::with('asset')->findOrFail($maintenance_id);
        
        // Verificar que el mantenimiento esté en progreso
        if ($maintenance->status !== 'in_progress') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'Solo se pueden editar mantenimientos en progreso');
        }
        
        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'procedure' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,critical',
            'estimated_time' => 'nullable|integer',
            'materials_used' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120',
        ]);
        
        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            // Actualizar mantenimiento
            $additionalInfo = json_decode($maintenance->additional_info ?? '{}', true);
            $additionalInfo['priority'] = $validated['priority'];
            $additionalInfo['estimated_time'] = $validated['estimated_time'];
            $additionalInfo['materials_used'] = $validated['materials_used'];
            $additionalInfo['recommendations'] = $validated['recommendations'];
            
            $maintenanceData = [
                'diagnosis' => $validated['diagnosis'],
                'procedure' => $validated['procedure'],
                'additional_info' => json_encode($additionalInfo),
            ];
            
            // Si se va a completar ahora
            if ($request->has('complete_now')) {
                $maintenanceData['status'] = 'completed';
                $maintenanceData['end_date'] = Carbon::now();
                
                // Actualizar el estado del activo
                $maintenance->asset->update(['status' => 'active']);
            }
            
            $maintenance->update($maintenanceData);
            
            // Procesar imágenes si se subieron
            if ($request->hasFile('images')) {
                $this->processMaintenanceImages($request->file('images'), $maintenance);
            }
            
            \DB::commit();
            
            // Si se ha completado, redirigir a generación de acta
            if ($request->has('complete_now')) {
                return redirect()->route('certificates.create', $maintenance->id)
                    ->with('success', 'Mantenimiento completado correctamente');
            }
            
            return redirect()->route('maintenance-flow.confirmation', $maintenance->id)
                ->with('success', 'Mantenimiento actualizado correctamente');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al actualizar el mantenimiento: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra pantalla de confirmación
     */
    public function confirmation($maintenance_id)
    {
        $maintenance = Maintenance::with(['asset.department', 'technician'])->findOrFail($maintenance_id);
        
        // Decodificar información adicional
        $additionalInfo = json_decode($maintenance->additional_info ?? '{}', true);
        
        return view('maintenance-flow.confirmation', compact('maintenance', 'additionalInfo'));
    }
    
    /**
     * Procesa imágenes de un mantenimiento
     */
    private function processMaintenanceImages($images, $maintenance)
    {
        // Aquí implementaríamos la lógica para guardar las imágenes
        // Para este ejemplo, asumimos que se guardarán en una carpeta
        // y los nombres se almacenarán en additional_info
        $additionalInfo = json_decode($maintenance->additional_info ?? '{}', true);
        $additionalInfo['images'] = $additionalInfo['images'] ?? [];
        
        foreach ($images as $image) {
            $filename = 'maintenance_' . $maintenance->id . '_' . time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('maintenance_images', $filename, 'public');
            $additionalInfo['images'][] = $path;
        }
        
        $maintenance->update(['additional_info' => json_encode($additionalInfo)]);
    }
}