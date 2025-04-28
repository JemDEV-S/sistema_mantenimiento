@extends('layouts.app')

@section('styles')
<style>
    .signature-container {
        width: 100%;
        margin: 0 auto;
    }
    
    #signatureCanvas {
        border: 1px solid #ccc;
        width: 100%;
        height: 200px;
        background-color: #fff;
    }
    
    .signature-actions {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }
    
    .signature-preview {
        width: 100%;
        margin-top: 20px;
        text-align: center;
    }
    
    .signature-preview img {
        max-width: 200px;
        border: 1px solid #eee;
        padding: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Firma Digital del Acta de Mantenimiento</h5>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Información del Acta</h6>
                            <p>
                                <strong>Código:</strong> {{ $certificate->code }}<br>
                                <strong>Fecha:</strong> {{ $certificate->generation_date->format('d/m/Y H:i') }}<br>
                                <strong>Estado:</strong> 
                                @if($certificate->status == 'pending')
                                    <span class="badge bg-warning">Pendiente de Firmas</span>
                                @elseif($certificate->status == 'completed')
                                    <span class="badge bg-success">Completada</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Mantenimiento Realizado</h6>
                            <p>
                                <strong>Equipo:</strong> {{ $certificate->maintenance->asset->name }}<br>
                                <strong>Técnico:</strong> {{ $certificate->maintenance->technician->name }}<br>
                                <strong>Departamento:</strong> {{ $certificate->maintenance->asset->department->name }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between mb-3">
                                <h6>Estado de Firmas</h6>
                                <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> Descargar Acta
                                </a>
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
                        </div>
                    </div>
                    
                    @if($canSign)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6>Firma Digital como {{ $signatureType == 'technician' ? 'Técnico' : 'Jefe de Departamento' }}</h6>
                                    <p>Por favor, firme en el espacio a continuación para validar el acta de mantenimiento.</p>
                                </div>
                                
                                <form id="signatureForm" action="{{ route('certificates.registerSignature', $certificate->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="signature_type" value="{{ $signatureType }}">
                                    <input type="hidden" id="digital_signature" name="digital_signature" value="">
                                    
                                    <div class="signature-container">
                                        <canvas id="signatureCanvas"></canvas>
                                        <div class="signature-actions">
                                            <button type="button" id="clearSignature" class="btn btn-secondary">Limpiar</button>
                                            <button type="button" id="saveSignature" class="btn btn-primary">Guardar Firma</button>
                                        </div>
                                    </div>
                                    
                                    <div class="signature-preview mt-3 d-none" id="signaturePreview">
                                        <h6>Vista previa de la firma:</h6>
                                        <img id="signatureImage" src="" alt="Firma digital">
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-success">Confirmar y Registrar Firma</button>
                                            <button type="button" id="cancelSignature" class="btn btn-danger">Cancelar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @elseif($certificate->isCompletelySignaned())
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <h6>Acta completamente firmada</h6>
                                    <p>El acta ha sido firmada por todos los responsables requeridos.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h6>No puede firmar este documento</h6>
                                    <p>No tiene permisos para firmar este documento o ya ha registrado su firma.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-secondary">Volver a Detalles del Acta</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
        
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
        
        // Clear signature
        document.getElementById('clearSignature').addEventListener('click', function() {
            signaturePad.clear();
        });
        
        // Save signature
        document.getElementById('saveSignature').addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                alert('Por favor, proporcione una firma antes de continuar.');
                return;
            }
            
            const signatureData = signaturePad.toDataURL();
            const signatureImage = document.getElementById('signatureImage');
            signatureImage.src = signatureData;
            
            // Save base64 signature in hidden field
            const base64Data = signatureData.split(',')[1];
            document.getElementById('digital_signature').value = base64Data;
            
            // Show preview
            document.getElementById('signaturePreview').classList.remove('d-none');
        });
        
        // Cancel signature
        document.getElementById('cancelSignature').addEventListener('click', function() {
            document.getElementById('signaturePreview').classList.add('d-none');
            document.getElementById('digital_signature').value = '';
        });
        
        // Validate before submitting
        document.getElementById('signatureForm').addEventListener('submit', function(event) {
            if (document.getElementById('digital_signature').value === '') {
                event.preventDefault();
                alert('Por favor, guarde su firma antes de continuar.');
            }
        });
    });
</script>
@endsection