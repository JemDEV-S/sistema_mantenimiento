@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalles del Acta</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('certificates.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download"></i> Descargar
                </a>
                @if($certificate->status == 'pending')
                    <a href="{{ route('certificates.sign', $certificate->id) }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-signature"></i> Firmar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-signature me-1"></i> Información del Acta
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Código:</th>
                            <td>{{ $certificate->code }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Generación:</th>
                            <td>{{ $certificate->generation_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($certificate->status == 'pending')
                                    <span class="badge bg-warning">Pendiente de Firmas</span>
                                @elseif($certificate->status == 'completed')
                                    <span class="badge bg-success">Completada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tools me-1"></i> Información del Mantenimiento
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>
                                <a href="{{ route('maintenances.show', $certificate->maintenance->id) }}">
                                    {{ $certificate->maintenance->id }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                @if($certificate->maintenance->maintenance_type == 'preventive')
                                    <span class="badge bg-success">Preventivo</span>
                                @elseif($certificate->maintenance->maintenance_type == 'corrective')
                                    <span class="badge bg-danger">Correctivo</span>
                                @else
                                    <span class="badge bg-info">Predictivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Técnico:</th>
                            <td>{{ $certificate->maintenance->technician->name }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Inicio:</th>
                            <td>{{ $certificate->maintenance->start_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Fin:</th>
                            <td>{{ $certificate->maintenance->end_date ? $certificate->maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</td>
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
                            <th style="width: 30%">Nombre:</th>
                            <td>
                                <a href="{{ route('assets.show', $certificate->maintenance->asset->id) }}">
                                    {{ $certificate->maintenance->asset->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>{{ $certificate->maintenance->asset->type }}</td>
                        </tr>
                        <tr>
                            <th>Marca/Modelo:</th>
                            <td>{{ $certificate->maintenance->asset->brand }} {{ $certificate->maintenance->asset->model }}</td>
                        </tr>
                        <tr>
                            <th>Serial:</th>
                            <td>{{ $certificate->maintenance->asset->serial }}</td>
                        </tr>
                        <tr>
                            <th>Departamento:</th>
                            <td>{{ $certificate->maintenance->asset->department->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-signature me-1"></i> Estado de Firmas
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Progreso general</h6>
                        @php
                            $totalSignatures = $certificate->signatures->count();
                            $requiredSignatures = 2;
                        @endphp
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalSignatures / $requiredSignatures) * 100 }}%;" 
                                aria-valuenow="{{ $totalSignatures }}" aria-valuemin="0" aria-valuemax="{{ $requiredSignatures }}">
                                {{ $totalSignatures }}/{{ $requiredSignatures }}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipo de Firma</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Fecha de Firma</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Técnico</td>
                                    <td>{{ $certificate->maintenance->technician->name }}</td>
                                    <td>
                                        @php
                                            $technicianSignature = $certificate->signatures->where('signature_type', 'technician')->first();
                                        @endphp
                                        
                                        @if($technicianSignature)
                                            <span class="badge bg-success">Firmado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $technicianSignature ? $technicianSignature->signature_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Jefe de Departamento</td>
                                    <td>{{ $certificate->maintenance->asset->department->manager->name ?? 'No asignado' }}</td>
                                    <td>
                                        @php
                                            $managerSignature = $certificate->signatures->where('signature_type', 'manager')->first();
                                        @endphp
                                        
                                        @if($managerSignature)
                                            <span class="badge bg-success">Firmado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $managerSignature ? $managerSignature->signature_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($certificate->status == 'pending')
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i> El acta está pendiente de firma. Por favor, complete todas las firmas requeridas.
                        </div>
                    @elseif($certificate->status == 'completed')
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i> El acta está completamente firmada.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-image me-1"></i> Previsualización de Firmas
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Firma Técnico</h6>
                            @if($technicianSignature)
                                <img src="data:image/png;base64,{{ $technicianSignature->digital_signature }}" alt="Firma Técnico" class="img-fluid border p-2 mb-2" style="max-height: 100px;">
                                <p class="small text-muted">
                                    {{ $technicianSignature->user->name }}<br>
                                    {{ $technicianSignature->signature_date->format('d/m/Y H:i') }}
                                </p>
                            @else
                                <div class="border p-3 mb-2 text-muted">
                                    <i class="fas fa-signature fa-3x mb-2"></i><br>
                                    Pendiente
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Firma Jefe de Departamento</h6>
                            @if($managerSignature)
                                <img src="data:image/png;base64,{{ $managerSignature->digital_signature }}" alt="Firma Jefe" class="img-fluid border p-2 mb-2" style="max-height: 100px;">
                                <p class="small text-muted">
                                    {{ $managerSignature->user->name }}<br>
                                    {{ $managerSignature->signature_date->format('d/m/Y H:i') }}
                                </p>
                            @else
                                <div class="border p-3 mb-2 text-muted">
                                    <i class="fas fa-signature fa-3x mb-2"></i><br>
                                    Pendiente
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-pdf me-1"></i> Opciones del Documento
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i> Descargar Acta
                        </a>
                        @if($certificate->status == 'pending')
                            <a href="{{ route('certificates.sign', $certificate->id) }}" class="btn btn-success">
                                <i class="fas fa-signature me-1"></i> Ir a Firmar
                            </a>
                        @endif
                        <a href="{{ route('maintenances.show', $certificate->maintenance->id) }}" class="btn btn-info">
                            <i class="fas fa-tools me-1"></i> Ver Mantenimiento
                        </a>
                        <a href="{{ route('assets.show', $certificate->maintenance->asset->id) }}" class="btn btn-secondary">
                            <i class="fas fa-desktop me-1"></i> Ver Activo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
