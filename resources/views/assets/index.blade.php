@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Activos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('assets.sync') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sync"></i> Sincronizar con OCS
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros de búsqueda
        </div>
        <div class="card-body">
            <form action="{{ route('assets.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre, serial, marca...">
                </div>
                <div class="col-md-3">
                    <label for="patrimony_code" class="form-label">Código Patrimonial</label>
                    <input type="text" class="form-control" id="patrimony_code" name="patrimony_code" value="{{ request('patrimony_code') }}" placeholder="Código...">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Tipo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Todos</option>
                        @foreach($assetTypes as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Departamento</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">Todos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-desktop me-1"></i> Listado de Activos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Marca/Modelo</th>
                            <th>Serial</th>
                            <th>Código Patrimonial</th>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th>Última Sincronización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>{{ $asset->id }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->type }}</td>
                                <td>{{ $asset->brand }} {{ $asset->model }}</td>
                                <td>{{ $asset->serial }}</td>
                                <td>{{ $asset->patrimony_code ?? 'N/A' }}</td>
                                <td>{{ $asset->department->name }}</td>
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
                                <td>{{ $asset->last_sync ? $asset->last_sync->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('maintenances.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-primary" title="Crear mantenimiento">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-3">No hay activos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $assets->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection