<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'certificate_id',
        'user_id',
        'signature_type',
        'signature_date',
        'digital_signature',
    ];
    
    protected $casts = [
        'signature_date' => 'datetime',
    ];
    
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}