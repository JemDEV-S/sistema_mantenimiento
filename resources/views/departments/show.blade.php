@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalles del Departamento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar el departamento <strong>{{ $department->name }}</strong>?</p>
                    
                    @if($department->assets->count() > 0 || $department->users->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Este departamento tiene:
                            <ul class="mb-0 mt-1">
                                @if($department->assets->count() > 0)
                                    <li>{{ $department->assets->count() }} activo(s) asignado(s)</li>
                                @endif
                                @if($department->users->count() > 0)
                                    <li>{{ $department->users->count() }} usuario(s) asignado(s)</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" {{ ($department->assets->count() > 0 || $department->users->count() > 0) ? 'disabled' : '' }}>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-building me-1"></i> Información del Departamento
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $department->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $department->name }}</td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td>{{ $department->code }}</td>
                        </tr>
                        <tr>
                            <th>Ubicación:</th>
                            <td>{{ $department->location }}</td>
                        </tr>
                        <tr>
                            <th>Responsable:</th>
                            <td>
                                @if($department->manager)
                                    <a href="{{ route('users.show', $department->manager->id) }}">
                                        {{ $department->manager->name }}
                                    </a>
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Activos:</th>
                            <td>{{ $department->assets->count() }}</td>
                        </tr>
                        <tr>
                            <th>Usuarios:</th>
                            <td>{{ $department->users->count() }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $department->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $department->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Usuarios Asignados
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Rol</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($department->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->position }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->role->name }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No hay usuarios asignados a este departamento.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-desktop me-1"></i> Activos Asignados
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('assets.index', ['department_id' => $department->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-search"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Marca/Modelo</th>
                                    <th>Serial</th>
                                    <th>Código P.</th>
                                    <th>Estado</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($department->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->type }}</td>
                                        <td>{{ $asset->brand }} {{ $asset->model }}</td>
                                        <td>{{ $asset->serial }}</td>
                                        <td>{{ $asset->patrimony_code ?? 'N/A' }}</td>
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
                                        <td>
                                            <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No hay activos asignados a este departamento.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection