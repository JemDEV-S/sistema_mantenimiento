@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('assets.sync') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sync"></i> Sincronizar con OCS
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Activos</h6>
                            <h2 class="mb-0">{{ $totalAssets }}</h2>
                        </div>
                        <i class="fas fa-desktop fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('assets.index') }}" class="text-white text-decoration-none small">
                        Ver detalle <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Mantenimientos Pendientes</h6>
                            <h2 class="mb-0">{{ $pendingMaintenances }}</h2>
                        </div>
                        <i class="fas fa-tools fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('maintenances.index') }}?status=in_progress" class="text-dark text-decoration-none small">
                        Ver detalle <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Actas Pendientes</h6>
                            <h2 class="mb-0">{{ $pendingCertificates }}</h2>
                        </div>
                        <i class="fas fa-file-signature fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('certificates.index') }}?status=pending" class="text-dark text-decoration-none small">
                        Ver detalle <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Mi Departamento</h6>
                            <h5 class="mb-0">{{ Auth::user()->department->name }}</h5>
                        </div>
                        <i class="fas fa-building fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <span class="text-white small">
                        <i class="fas fa-user-tie me-1"></i> {{ Auth::user()->department->manager->name ?? 'No asignado' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list me-1"></i> Mantenimientos Recientes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Equipo</th>
                                    <th>Técnico</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMaintenances as $maintenance)
                                    <tr>
                                        <td>{{ $maintenance->id }}</td>
                                        <td>{{ $maintenance->asset->name }}</td>
                                        <td>{{ $maintenance->technician->name }}</td>
                                        <td>
                                            @if($maintenance->maintenance_type == 'preventive')
                                                <span class="badge bg-success">Preventivo</span>
                                            @elseif($maintenance->maintenance_type == 'corrective')
                                                <span class="badge bg-danger">Correctivo</span>
                                            @else
                                                <span class="badge bg-info">Predictivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($maintenance->status == 'in_progress')
                                                <span class="badge bg-warning">En Proceso</span>
                                            @elseif($maintenance->status == 'completed')
                                                <span class="badge bg-success">Completado</span>
                                            @endif
                                        </td>
                                        <td>{{ $maintenance->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No hay mantenimientos recientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver todos los mantenimientos
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tachometer-alt me-1"></i> Estado del Sistema
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Conexión con OCS Inventory</span>
                        <span class="badge bg-success">Conectado</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Última sincronización</span>
                        <span>{{ now()->subHours(rand(1, 24))->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Base de datos</span>
                        <span class="badge bg-success">Operativa</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Versión del sistema</span>
                        <span>1.0.0</span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-bell me-1"></i> Notificaciones
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-file-signature text-info me-2"></i>
                                    Acta pendiente de firma
                                </div>
                                <span class="badge bg-primary">Nuevo</span>
                            </div>
                            <small class="text-muted">Hace 2 horas</small>
                        </li>
                        <li class="list-group-item">
                            <div>
                                <i class="fas fa-sync-alt text-success me-2"></i>
                                Sincronización completada
                            </div>
                            <small class="text-muted">Hace 1 día</small>
                        </li>
                        <li class="list-group-item">
                            <div>
                                <i class="fas fa-tools text-warning me-2"></i>
                                Nuevo mantenimiento asignado
                            </div>
                            <small class="text-muted">Hace 2 días</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
