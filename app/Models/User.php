<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'role_id',
        'department_id',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }
    
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'technician_id');
    }
    
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
    
    public function isAdmin()
    {
        return $this->role->name === 'Administrator';
    }
    
    public function isTechnician()
    {
        return $this->role->name === 'Technician';
    }
    
    public function isManager()
    {
        return $this->role->name === 'Manager';
    }
}