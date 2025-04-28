@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalles del Activo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('maintenances.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-tools"></i> Crear Mantenimiento
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Información Básica
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $asset->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $asset->name }}</td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>{{ $asset->type }}</td>
                        </tr>
                        <tr>
                            <th>Marca:</th>
                            <td>{{ $asset->brand ?? 'No especificada' }}</td>
                        </tr>
                        <tr>
                            <th>Modelo:</th>
                            <td>{{ $asset->model ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Serial:</th>
                            <td>{{ $asset->serial ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Código Patrimonial:</th>
                            <td>{{ $asset->patrimony_code ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($asset->status == 'active')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($asset->status == 'in_maintenance')
                                    <span class="badge bg-warning">En Mantenimiento</span>
                                @elseif($asset->status == 'inactive')
                                    <span class="badge bg-secondary">Inactivo</span>
                                @else
                                    <span class="badge bg-danger">De Baja</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Departamento:</th>
                            <td>{{ $asset->department->name }}</td>
                        </tr>
                        <tr>
                            <th>Última Sincronización:</th>
                            <td>{{ $asset->last_sync ? $asset->last_sync->format('d/m/Y H:i') : 'Nunca' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-server me-1"></i> Información de OCS Inventory
                </div>
                <div class="card-body">
                    @if($ocsDetails)
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%">ID OCS:</th>
                                <td>{{ $ocsDetails->id }}</td>
                            </tr>
                            <tr>
                                <th>Sistema Operativo:</th>
                                <td>{{ $ocsDetails->operating_system ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <th>Versión SO:</th>
                                <td>{{ $ocsDetails->os_version ?? 'No especificada' }}</td>
                            </tr>
                            <tr>
                                <th>Procesador:</th>
                                <td>{{ $ocsDetails->processor ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <th>Memoria RAM:</th>
                                <td>{{ $ocsDetails->memory ?? 'No especificada' }}</td>
                            </tr>
                            <tr>
                                <th>Último Acceso:</th>
                                <td>{{ $ocsDetails->last_access ?? 'No registrado' }}</td>
                            </tr>
                        </table>

                        @if(isset($ocsDetails->disks) && count($ocsDetails->disks) > 0)
                            <div class="mt-3">
                                <h6>Discos</h6>
                                <ul class="list-group">
                                    @foreach($ocsDetails->disks as $disk)
                                        <li class="list-group-item">
                                            {{ $disk->NAME ?? 'Sin nombre' }} - {{ $disk->TYPE ?? 'N/A' }} 
                                            ({{ $disk->DISKSIZE ?? 'N/A' }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(isset($ocsDetails->networks) && count($ocsDetails->networks) > 0)
                            <div class="mt-3">
                                <h6>Interfaces de Red</h6>
                                <ul class="list-group">
                                    @foreach($ocsDetails->networks as $network)
                                        <li class="list-group-item">
                                            {{ $network->DESCRIPTION ?? 'Sin descripción' }} - 
                                            IP: {{ $network->IPADDRESS ?? 'N/A' }} - 
                                            MAC: {{ $network->MACADDR ?? 'N/A' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            No se encontró información adicional en OCS Inventory.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-history me-1"></i> Historial de Mantenimientos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asset->maintenances as $maintenance)
                            <tr>
                                <td>{{ $maintenance->id }}</td>
                                <td>
                                    @if($maintenance->maintenance_type == 'preventive')
                                        <span class="badge bg-success">Preventivo</span>
                                    @elseif($maintenance->maintenance_type == 'corrective')
                                        <span class="badge bg-danger">Correctivo</span>
                                    @else
                                        <span class="badge bg-info">Predictivo</span>
                                    @endif
                                </td>
                                <td>{{ $maintenance->technician->name }}</td>
                                <td>{{ $maintenance->start_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</td>
                                <td>
                                    @if($maintenance->status == 'in_progress')
                                        <span class="badge bg-warning">En Proceso</span>
                                    @elseif($maintenance->status == 'completed')
                                        <span class="badge bg-success">Completado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($maintenance->certificate)
                                        <a href="{{ route('certificates.show', $maintenance->certificate->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-signature"></i> {{ $maintenance->certificate->code }}
                                        </a>
                                    @else
                                        @if($maintenance->status == 'completed')
                                            <a href="{{ route('certificates.create', $maintenance->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-plus-circle"></i> Generar
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">No disponible</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">No hay mantenimientos registrados para este activo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection