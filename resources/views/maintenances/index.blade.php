@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Mantenimientos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('maintenance-flow.select-asset') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus"></i> Nuevo Mantenimiento
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros de búsqueda
        </div>
        <div class="card-body">
            <form action="{{ route('maintenances.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for "end_date" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-tools me-1"></i> Listado de Mantenimientos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Activo</th>
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
                        @forelse($maintenances as $maintenance)
                            <tr>
                                <td>{{ $maintenance->id }}</td>
                                <td>
                                    <a href="{{ route('assets.show', $maintenance->asset->id) }}">
                                        {{ $maintenance->asset->name }}
                                    </a>
                                </td>
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
                                    <div class="btn-group">
                                        <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($maintenance->status == 'in_progress')
                                            <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" title="Completar"
                                                onclick="document.getElementById('complete-form-{{ $maintenance->id }}').submit()">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <form id="complete-form-{{ $maintenance->id }}" action="{{ route('maintenances.complete', $maintenance->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-3">No hay mantenimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $maintenances->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection