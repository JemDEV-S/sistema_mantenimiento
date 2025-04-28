@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalles del Usuario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning">
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
                    ¿Está seguro de que desea eliminar al usuario <strong>{{ $user->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i> Información del Usuario
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Cargo:</th>
                            <td>{{ $user->position }}</td>
                        </tr>
                        <tr>
                            <th>Rol:</th>
                            <td>
                                <span class="badge bg-info">{{ $user->role->name }}</span>
                                <small class="text-muted d-block mt-1">{{ $user->role->description }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Departamento:</th>
                            <td>{{ $user->department->name }} ({{ $user->department->code }})</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tools me-1"></i> Mantenimientos Realizados
                </div>
                <div class="card-body p-0">
                    @if($user->role->name == 'Technician')
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Activo</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->maintenances ?? [] as $maintenance)
                                        <tr>
                                            <td>{{ $maintenance->id }}</td>
                                            <td>{{ $maintenance->asset->name }}</td>
                                            <td>
                                                @if($maintenance->maintenance_type == 'preventive')
                                                    <span class="badge bg-success">Preventivo</span>
                                                @elseif($maintenance->maintenance_type == 'corrective')
                                                    <span class="badge bg-danger">Correctivo</span>
                                                @else
                                                    <span class="badge bg-info">Predictivo</span>
                                                @endif
                                            </td>
                                            <td>{{ $maintenance->start_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($maintenance->status == 'in_progress')
                                                    <span class="badge bg-warning">En Proceso</span>
                                                @elseif($maintenance->status == 'completed')
                                                    <span class="badge bg-success">Completado</span>
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
                                            <td colspan="6" class="text-center py-3">No hay mantenimientos registrados por este técnico.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-3">
                            Este usuario no tiene el rol de técnico.
                        </div>
                    @endif
                </div>
            </div>

            @if($user->department && $user->id == $user->department->manager_id)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-building me-1"></i> Responsable del Departamento
                    </div>
                    <div class="card-body">
                        <p>Este usuario es el responsable del departamento <strong>{{ $user->department->name }}</strong>.</p>
                        
                        <h6 class="mt-3">Activos asignados al departamento:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->department->assets->take(5) as $asset)
                                        <tr>
                                            <td>{{ $asset->id }}</td>
                                            <td>{{ $asset->name }}</td>
                                            <td>{{ $asset->type }}</td>
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
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay activos asignados al departamento.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($user->department->assets->count() > 5)
                            <div class="text-center mt-2">
                                <a href="{{ route('departments.show', $user->department->id) }}" class="btn btn-sm btn-outline-primary">
                                    Ver todos los activos ({{ $user->department->assets->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-file-signature me-1"></i> Firmas Realizadas
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Acta</th>
                            <th>Tipo de Firma</th>
                            <th>Fecha de Firma</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->signatures ?? [] as $signature)
                            <tr>
                                <td>{{ $signature->id }}</td>
                                <td>{{ $signature->certificate->code }}</td>
                                <td>
                                    @if($signature->signature_type == 'technician')
                                        <span class="badge bg-primary">Técnico</span>
                                    @elseif($signature->signature_type == 'manager')
                                        <span class="badge bg-info">Responsable</span>
                                    @endif
                                </td>
                                <td>{{ $signature->signature_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('certificates.show', $signature->certificate->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver Acta
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">No hay firmas registradas por este usuario.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection