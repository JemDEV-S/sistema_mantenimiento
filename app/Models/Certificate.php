<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'maintenance_id',
        'code',
        'status',
        'file_path',
        'generation_date',
        'additional_info', // También podemos añadir esta columna para el acta si es necesario
    ];
    
    protected $casts = [
        'generation_date' => 'datetime',
        'additional_info' => 'json',
    ];
    
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
    
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
    
    /**
     * Verifica si el acta está firmada por el técnico
     */
    public function isSignedByTechnician()
    {
        return $this->signatures()->where('signature_type', 'technician')->exists();
    }
    
    /**
     * Verifica si el acta está firmada por el jefe de departamento
     */
    public function isSignedByManager()
    {
        return $this->signatures()->where('signature_type', 'manager')->exists();
    }
    
    /**
     * Verifica si el acta está completamente firmada
     */
    public function isCompletelySignaned() // Nombre original mantenido para compatibilidad
    {
        return $this->isSignedByTechnician() && $this->isSignedByManager();
    }
    
    /**
     * Verifica si el acta está completamente firmada (nombre alternativo más claro)
     */
    public function isFullySigned()
    {
        return $this->isCompletelySignaned();
    }
    
    /**
     * Obtener el estado del acta en formato legible
     */
    public function getStatusTextAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'Pendiente de Firmas';
            case 'completed':
                return 'Completada';
            default:
                return ucfirst($this->status);
        }
    }
}