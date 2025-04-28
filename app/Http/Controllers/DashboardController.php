<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas para el dashboard
        $totalAssets = Asset::count();
        $recentMaintenances = Maintenance::with(['asset', 'technician'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $pendingMaintenances = Maintenance::where('status', 'in_progress')->count();
        $pendingCertificates = Certificate::where('status', 'pending')->count();
        
        return view('dashboard.index', compact(
            'totalAssets', 
            'recentMaintenances', 
            'pendingMaintenances',
            'pendingCertificates'
        ));
    }
}