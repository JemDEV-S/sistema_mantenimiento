<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'asset_id',
        'technician_id',
        'maintenance_type',
        'diagnosis',
        'procedure',
        'status',
        'start_date',
        'end_date',
        'additional_info', // Nueva columna para información adicional
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'additional_info' => 'json', // Esto permitirá trabajar con JSON automáticamente
    ];
    
    /**
     * Obtener la información adicional como un array
     */
    public function getAdditionalDataAttribute()
    {
        return json_decode($this->additional_info ?? '{}', true);
    }
    
    /**
     * Obtener la prioridad del mantenimiento
     */
    public function getPriorityAttribute()
    {
        $data = $this->additional_data;
        return $data['priority'] ?? null;
    }
    
    /**
     * Obtener el tiempo estimado
     */
    public function getEstimatedTimeAttribute()
    {
        $data = $this->additional_data;
        return $data['estimated_time'] ?? null;
    }
    
    /**
     * Obtener los materiales utilizados
     */
    public function getMaterialsUsedAttribute()
    {
        $data = $this->additional_data;
        return $data['materials_used'] ?? null;
    }
    
    /**
     * Obtener las recomendaciones
     */
    public function getRecommendationsAttribute()
    {
        $data = $this->additional_data;
        return $data['recommendations'] ?? null;
    }
    
    /**
     * Obtener las imágenes
     */
    public function getImagesAttribute()
    {
        $data = $this->additional_data;
        return $data['images'] ?? [];
    }
    
    /**
     * Obtener información del próximo mantenimiento
     */
    public function getNextMaintenanceAttribute()
    {
        $data = $this->additional_data;
        return [
            'date' => $data['next_maintenance_date'] ?? null,
            'type' => $data['next_maintenance_type'] ?? null,
        ];
    }
    
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
    
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}