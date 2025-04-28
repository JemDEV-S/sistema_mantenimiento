<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ocs_id',
        'name',
        'type',
        'brand',
        'model',
        'serial',
        'patrimony_code', // Campo traducido al inglés
        'status',
        'department_id',
        'last_sync',
    ];
    
    protected $casts = [
        'last_sync' => 'datetime',
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}