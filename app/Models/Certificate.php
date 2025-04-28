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
    ];
    
    protected $casts = [
        'generation_date' => 'datetime',
    ];
    
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
    
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
    
    public function isSignedByTechnician()
    {
        return $this->signatures()->where('signature_type', 'technician')->exists();
    }
    
    public function isSignedByManager()
    {
        return $this->signatures()->where('signature_type', 'manager')->exists();
    }
    
    public function isCompletelySignaned()
    {
        return $this->isSignedByTechnician() && $this->isSignedByManager();
    }
}