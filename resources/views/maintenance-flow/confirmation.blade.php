@extends('layouts.app')

@section('styles')
<style>
    .confirmation-card {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }
    
    .confirmation-header {
        background-color: #28a745;
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .success-icon {
        font-size: 5rem;
        margin-bottom: 1rem;
    }
    
    .confirmation-body {
        padding: 2rem;
    }
    
    .maintenance-info {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-section {
        margin-bottom: 1.5rem;
    }
    
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .action-button {
        flex: 1;
        min-width: 200px;
        max-width: 300px;
        border-radius: 0.5rem;
        padding: 1.5rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .action-button:hover {
        transform: translateY(-5px);
    }
    
    .action-button i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .action-button h5 {
        margin-bottom: 0.5rem;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .container-fluid {
            width: 100%;
            padding: 0;
            margin: 0;
        }
        
        .confirmation-card {
            box-shadow: none;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
        <h1 class="h2">Confirmación de Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('maintenance-flow.select-asset') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Nuevo Mantenimiento
                </a>
                <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-list"></i> Listado de Mantenimientos
                </a>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
    
    <div class="confirmation-card">
        <div class="confirmation-header">
            <i class="fas fa-check-circle success-icon"></i>
            <h2>¡Mantenimiento Registrado con Éxito!</h2>
            <p class="lead mb-0">El mantenimiento ha sido registrado correctamente en el sistema</p>
        </div>
        
        <div class="confirmation-body">
            <div class="maintenance-info">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h5><i class="fas fa-clipboard-check me-2"></i> Información del Mantenimiento</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 30%">ID:</th>
                                        <td>{{ $maintenance->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Inicio:</th>
                                        <td>{{ $maintenance->start_date->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
                                        <td>
                                            @if($maintenance->maintenance_type == 'preventive')
                                                <span class="badge bg-success">Preventivo</span>
                                            @elseif($maintenance->maintenance_type == 'corrective')
                                                <span class="badge bg-danger">Correctivo</span>
                                            @else
                                                <span class="badge bg-info">Predictivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Estado:</th>
                                        <td>
                                            @if($maintenance->status == 'in_progress')
                                                <span class="badge bg-warning">En Proceso</span>
                                            @elseif($maintenance->status == 'completed')
                                                <span class="badge bg-success">Completado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Prioridad:</th>
                                        <td>
                                            @php
                                                $priorityLabel = 'No especificada';
                                                $priorityClass = 'secondary';
                                                
                                                if (!empty($additionalInfo['priority'])) {
                                                    switch($additionalInfo['priority']) {
                                                        case 'low':
                                                            $priorityLabel = 'Baja';
                                                            $priorityClass = 'success';
                                                            break;
                                                        case 'medium':
                                                            $priorityLabel = 'Media';
                                                            $priorityClass = 'primary';
                                                            break;
                                                        case 'high':
                                                            $priorityLabel = 'Alta';
                                                            $priorityClass = 'warning';
                                                            break;
                                                        case 'critical':
                                                            $priorityLabel = 'Crítica';
                                                            $priorityClass = 'danger';
                                                            break;
                                                    }
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $priorityClass }}">{{ $priorityLabel }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Técnico:</th>
                                        <td>{{ $maintenance->technician->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-section">
                            <h5><i class="fas fa-desktop me-2"></i> Información del Activo</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 30%">Nombre:</th>
                                        <td>{{ $maintenance->asset->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
                                        <td>{{ $maintenance->asset->type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marca/Modelo:</th>
                                        <td>{{ $maintenance->asset->brand }} {{ $maintenance->asset->model }}</td>
                                    </tr>
                                    <tr>
                                        <th>Serial:</th>
                                        <td>{{ $maintenance->asset->serial }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departamento:</th>
                                        <td>{{ $maintenance->asset->department->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <h5><i class="fas fa-stethoscope me-2"></i> Diagnóstico</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! $maintenance->diagnosis !!}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-section">
                        <h5><i class="fas fa-wrench me-2"></i> Procedimiento</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! $maintenance->procedure !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(!empty($additionalInfo['materials_used']) || !empty($additionalInfo['recommendations']))
            <div class="row">
                @if(!empty($additionalInfo['materials_used']))
                <div class="col-md-6">
                    <div class="info-section">
                        <h5><i class="fas fa-boxes me-2"></i> Materiales Utilizados</h5>
                        <div class="card">
                            <div class="card-body">
                                <pre>{{ $additionalInfo['materials_used'] }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(!empty($additionalInfo['recommendations']))
                <div class="col-md-6">
                    <div class="info-section">
                        <h5><i class="fas fa-lightbulb me-2"></i> Recomendaciones</h5>
                        <div class="card">
                            <div class="card-body">
                                <pre>{{ $additionalInfo['recommendations'] }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
            
            <div class="no-print">
                <hr>
                
                @if($maintenance->status == 'in_progress')
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5>Mantenimiento En Progreso</h5>
                            <p class="mb-0">Este mantenimiento está en progreso. El activo permanecerá en estado "En Mantenimiento" hasta que complete el proceso.</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-success">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5>Mantenimiento Completado</h5>
                            <p class="mb-0">El mantenimiento ha sido completado y el activo ha vuelto a su estado "Activo".</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="action-buttons">
                    <a href="{{ route('maintenances.show', $maintenance->id) }}" class="action-button card bg-primary text-white text-decoration-none">
                        <i class="fas fa-eye"></i>
                        <h5>Ver Detalle Completo</h5>
                        <p class="mb-0">Acceder a la vista detallada del mantenimiento</p>
                    </a>
                    
                    @if($maintenance->status == 'in_progress')
                    <a href="{{ route('maintenance-flow.maintenance-form-edit', $maintenance->id) }}" class="action-button card bg-warning text-dark text-decoration-none">
                        <i class="fas fa-edit"></i>
                        <h5>Continuar Edición</h5>
                        <p class="mb-0">Continuar trabajando en este mantenimiento</p>
                    </a>
                    @endif
                    
                    @if($maintenance->status == 'completed' && !$maintenance->certificate)
                    <a href="{{ route('certificates.create', $maintenance->id) }}" class="action-button card bg-success text-white text-decoration-none">
                        <i class="fas fa-file-signature"></i>
                        <h5>Generar Acta</h5>
                        <p class="mb-0">Crear acta de mantenimiento para firma</p>
                    </a>
                    @elseif($maintenance->certificate)
                    <a href="{{ route('certificates.show', $maintenance->certificate->id) }}" class="action-button card bg-info text-white text-decoration-none">
                        <i class="fas fa-file-alt"></i>
                        <h5>Ver Acta</h5>
                        <p class="mb-0">Ver el acta de este mantenimiento</p>
                    </a>
                    @endif
                    
                    <a href="{{ route('maintenance-flow.select-asset') }}" class="action-button card bg-secondary text-white text-decoration-none">
                        <i class="fas fa-plus-circle"></i>
                        <h5>Nuevo Mantenimiento</h5>
                        <p class="mb-0">Registrar un nuevo mantenimiento</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection