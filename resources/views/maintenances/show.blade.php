@extends('layouts.app')

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
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
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
                    <i class="fas fa-stethoscope me-1"></i> Diagnóstico
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $maintenance->diagnosis }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-wrench me-1"></i> Procedimiento Realizado
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $maintenance->procedure }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
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
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('assets.show', $maintenance->asset->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Ver Detalles del Activo
                        </a>
                    </div>
                </div>
            </div>

            @if($maintenance->certificate)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-signature me-1"></i> Información del Acta
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Código:</th>
                            <td>{{ $maintenance->certificate->code }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($maintenance->certificate->status == 'pending')
                                    <span class="badge bg-warning">Pendiente de Firmas</span>
                                @elseif($maintenance->certificate->status == 'completed')
                                    <span class="badge bg-success">Completada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha Generación:</th>
                            <td>{{ $maintenance->certificate->generation_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Firmas:</th>
                            <td>
                                @php
                                    $totalSignatures = $maintenance->certificate->signatures->count();
                                    $requiredSignatures = 2;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalSignatures / $requiredSignatures) * 100 }}%;" 
                                        aria-valuenow="{{ $totalSignatures }}" aria-valuemin="0" aria-valuemax="{{ $requiredSignatures }}">
                                        {{ $totalSignatures }}/{{ $requiredSignatures }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <a href="{{ route('certificates.download', $maintenance->certificate->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                        <a href="{{ route('certificates.show', $maintenance->certificate->id) }}" class="btn btn-outline-info">
                            <i class="fas fa-search"></i> Ver Detalles
                        </a>
                        @if(!$maintenance->certificate->isCompletelySignaned())
                            <a href="{{ route('certificates.sign', $maintenance->certificate->id) }}" class="btn btn-outline-success">
                                <i class="fas fa-signature"></i> Firmar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection