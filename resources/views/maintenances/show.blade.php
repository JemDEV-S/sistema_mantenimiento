@extends('layouts.app')

@section('styles')
<style>
    .maintenance-info-section {
        margin-bottom: 1.5rem;
    }
    
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 1rem;
    }
    
    .gallery-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .gallery-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
        margin-top: 20px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-dot {
        position: absolute;
        left: -30px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #007bff;
        border: 3px solid #fff;
    }
    
    .timeline-content {
        padding: 10px 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    .priority-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
    }
    
    .priority-low {
        background-color: #28a745;
    }
    
    .priority-medium {
        background-color: #007bff;
    }
    
    .priority-high {
        background-color: #ffc107;
    }
    
    .priority-critical {
        background-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalles del Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                @if($maintenance->status == 'in_progress')
                    <a href="{{ route('maintenance-flow.maintenance-form-edit', $maintenance->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i> Editar (Flujo Mejorado)
                    </a>
                    <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-success" 
                        onclick="document.getElementById('complete-form').submit()">
                        <i class="fas fa-check"></i> Completar
                    </button>
                    <form id="complete-form" action="{{ route('maintenances.complete', $maintenance->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')
                    </form>
                @endif
                @if($maintenance->status == 'completed' && !$maintenance->certificate)
                    <a href="{{ route('certificates.create', $maintenance->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-signature"></i> Generar Acta
                    </a>
                @endif
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Información del Mantenimiento
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $maintenance->id }}</td>
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
                        @if($maintenance->priority)
                        <tr>
                            <th>Prioridad:</th>
                            <td>
                                @php
                                    $priorityLabel = 'No especificada';
                                    $priorityClass = 'secondary';
                                    
                                    switch($maintenance->priority) {
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
                                @endphp
                                <span class="priority-indicator priority-{{ $maintenance->priority }}"></span>
                                <span class="badge bg-{{ $priorityClass }}">{{ $priorityLabel }}</span>
                            </td>
                        </tr>
                        @endif
                        @if($maintenance->estimated_time)
                        <tr>
                            <th>Tiempo Estimado:</th>
                            <td>{{ $maintenance->estimated_time }} minutos</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Técnico:</th>
                            <td>{{ $maintenance->technician->name }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Inicio:</th>
                            <td>{{ $maintenance->start_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Fin:</th>
                            <td>{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</td>
                        </tr>
                        @if($maintenance->end_date && $maintenance->start_date)
                        <tr>
                            <th>Duración:</th>
                            <td>
                                @php
                                    $duration = $maintenance->start_date->diff($maintenance->end_date);
                                    $durationText = '';
                                    
                                    if ($duration->days > 0) {
                                        $durationText .= $duration->days . ' día(s), ';
                                    }
                                    
                                    $durationText .= $duration->h . ' hora(s), ' . $duration->i . ' minuto(s)';
                                @endphp
                                {{ $durationText }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Acta:</th>
                            <td>
                                @if($maintenance->certificate)
                                    <a href="{{ route('certificates.show', $maintenance->certificate->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-signature"></i> {{ $maintenance->certificate->code }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">No generada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-desktop me-1"></i> Información del Activo
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $maintenance->asset->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
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
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($maintenance->asset->status == 'active')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($maintenance->asset->status == 'in_maintenance')
                                    <span class="badge bg-warning">En Mantenimiento</span>
                                @elseif($maintenance->asset->status == 'inactive')
                                    <span class="badge bg-secondary">Inactivo</span>
                                @else
                                    <span class="badge bg-danger">De Baja</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div class="d-grid mt-2">
                        <a href="{{ route('assets.show', $maintenance->asset->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-search"></i> Ver Detalles del Activo
                        </a>
                    </div>
                </div>
            </div>
            
            @if($maintenance->materials_used)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-boxes me-1"></i> Materiales Utilizados
                </div>
                <div class="card-body">
                    <pre class="mb-0">{{ $maintenance->materials_used }}</pre>
                </div>
            </div>
            @endif
            
            @if($maintenance->recommendations)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-lightbulb me-1"></i> Recomendaciones
                </div>
                <div class="card-body">
                    <pre class="mb-0">{{ $maintenance->recommendations }}</pre>
                </div>
            </div>
            @endif
            
            @if(count($maintenance->images) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-images me-1"></i> Imágenes
                </div>
                <div class="card-body">
                    <div class="image-gallery">
                        @foreach($maintenance->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" class="gallery-image" alt="Imagen de mantenimiento" 
                                onclick="window.open('{{ asset('storage/' . $image) }}', '_blank')">
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-1"></i> Diagnóstico
                </div>
                <div class="card-body">
                    <div class="maintenance-info-section">
                        {!! $maintenance->diagnosis !!}
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-wrench me-1"></i> Procedimiento Realizado
                </div>
                <div class="card-body">
                    <div class="maintenance-info-section">
                        {!! $maintenance->procedure !!}
                    </div>
                </div>
            </div>
            
            <!-- Próximo mantenimiento (si está programado) -->
            @if($maintenance->next_maintenance['date'] ?? false)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calendar-alt me-1"></i> Próximo Mantenimiento Programado
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha Programada:</strong> {{ \Carbon\Carbon::parse($maintenance->next_maintenance['date'])->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tipo:</strong> 
                                @if($maintenance->next_maintenance['type'] == 'preventive')
                                    <span class="badge bg-success">Preventivo</span>
                                @elseif($maintenance->next_maintenance['type'] == 'corrective')
                                    <span class="badge bg-danger">Correctivo</span>
                                @else
                                    <span class="badge bg-info">Predictivo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Este mantenimiento ha sido programado. Asegúrese de realizar el seguimiento correspondiente.
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Línea de tiempo del mantenimiento -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i> Historial
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Mantenimiento Registrado</h6>
                                <p>
                                    <small class="text-muted">{{ $maintenance->created_at->format('d/m/Y H:i') }}</small><br>
                                    El técnico {{ $maintenance->technician->name }} inició el mantenimiento.
                                </p>
                            </div>
                        </div>
                        
                        @if($maintenance->updated_at->gt($maintenance->created_at))
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Mantenimiento Actualizado</h6>
                                <p>
                                    <small class="text-muted">{{ $maintenance->updated_at->format('d/m/Y H:i') }}</small><br>
                                    Se realizaron actualizaciones al registro de mantenimiento.
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenance->status == 'completed')
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Mantenimiento Completado</h6>
                                <p>
                                    <small class="text-muted">{{ $maintenance->end_date->format('d/m/Y H:i') }}</small><br>
                                    El mantenimiento fue completado y el activo volvió a estado "Activo".
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenance->certificate)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Acta Generada</h6>
                                <p>
                                    <small class="text-muted">{{ $maintenance->certificate->generation_date->format('d/m/Y H:i') }}</small><br>
                                    Se generó el acta de mantenimiento con código {{ $maintenance->certificate->code }}.
                                </p>
                            </div>
                        </div>
                        
                        @foreach($maintenance->certificate->signatures as $signature)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Acta Firmada por {{ $signature->signature_type == 'technician' ? 'Técnico' : 'Jefe de Departamento' }}</h6>
                                <p>
                                    <small class="text-muted">{{ $signature->signature_date->format('d/m/Y H:i') }}</small><br>
                                    {{ $signature->user->name }} firmó el acta como {{ $signature->signature_type == 'technician' ? 'Técnico' : 'Jefe de Departamento' }}.
                                </p>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($maintenance->certificate->status == 'completed')
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h6>Acta Completada</h6>
                                <p>
                                    <small class="text-muted">{{ $maintenance->certificate->updated_at->format('d/m/Y H:i') }}</small><br>
                                    El acta fue completada con todas las firmas requeridas.
                                </p>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection