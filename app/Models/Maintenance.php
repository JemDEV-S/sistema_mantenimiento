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
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    
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