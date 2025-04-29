<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Mantenimiento - {{ $certificate->code }}</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 11pt;
        }
        
        .container {
            width: 100%;
            padding: 20px;
        }
        
        /* Encabezado con logo */
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            padding-top: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .logo-container {
            position: absolute;
            top: 0;
            left: 20px;
            width: 80px;
            height: 80px;
        }
        
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .certificate-metadata {
            margin-top: 5px;
            font-size: 12px;
            color: #555;
        }
        
        .code {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            margin-top: 5px;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #2c3e50;
            padding: 8px 15px;
            border-radius: 4px 4px 0 0;
            margin-bottom: 0;
        }
        
        .section-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 15px;
            background-color: #fff;
            border-radius: 0 0 4px 4px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 5px;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 170px;
            color: #2c3e50;
        }
        
        .info-value {
            flex: 1;
        }
        
        /* Campos de texto */
        .text-field {
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            background-color: #f9f9f9;
            min-height: 60px;
            border-radius: 4px;
            text-align: justify;
            font-size: 10.5pt;
        }
        
        /* Prioridad */
        .priority-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
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
        
        /* Materiales */
        .materials-list {
            margin: 0;
            padding-left: 20px;
        }
        
        /* Firmas */
        .signatures {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        
        .signature-block {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            margin: 15px 0 10px 0;
            border-top: 1px solid #000;
            width: 100%;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 12pt;
        }
        
        .signature-title {
            font-size: 10pt;
            color: #555;
            margin-top: 5px;
        }
        
        .signature-date {
            font-size: 9pt;
            color: #777;
            margin-top: 5px;
        }
        
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 5px;
            min-height: 40px;
        }
        
        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            font-size: 9pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        /* Datos técnicos y detalles */
        .two-columns {
            display: flex;
            justify-content: space-between;
        }
        
        .column {
            width: 48%;
        }
        
        .qr-code {
            text-align: right;
            margin-top: 10px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10pt;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        .recommendations-note {
            background-color: #fffacd;
            border-left: 4px solid #ffd700;
            padding: 10px;
            font-style: italic;
        }
        
        /* Imágenes */
        .images-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 15px;
            gap: 10px;
        }
        
        .maintenance-image {
            width: 30%;
            border: 1px solid #ddd;
            padding: 3px;
            background: white;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Tabla de firmas */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        .signatures-table th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
            border: 1px solid #ddd;
        }
        
        .signatures-table td {
            padding: 8px;
            font-size: 10pt;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado con logo -->
        <div class="header">
            <div class="logo-container">
                <!-- Aquí iría el logo de la Municipalidad -->
                <!-- Ejemplo: -->
                <!-- <img src="{{ public_path('images/logo.png') }}" alt="Logo Municipalidad" style="width: 100%;"> -->
                <!-- O usando un logo en Base64 -->
                <div style="width: 80px; height: 80px; background-color: #2c3e50; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-weight: bold; font-size: 16px;">
                    MSJ
                </div>
            </div>
            
            <div class="title">ACTA DE MANTENIMIENTO DE EQUIPOS TECNOLÓGICOS</div>
            <div class="certificate-metadata">
                MUNICIPALIDAD DISTRITAL DE SAN JERÓNIMO<br>
                OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN
            </div>
            <div class="code">{{ $certificate->code }}</div>
        </div>
        
        <!-- Sección 1: Información General -->
        <div class="section">
            <div class="section-title">1. INFORMACIÓN GENERAL</div>
            <div class="section-content">
                <div class="two-columns">
                    <div class="column">
                        <div class="info-row">
                            <div class="info-label">Fecha de Inicio:</div>
                            <div class="info-value">{{ $maintenance->start_date->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Fecha de Fin:</div>
                            <div class="info-value">{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Departamento:</div>
                            <div class="info-value">{{ $department->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Ubicación:</div>
                            <div class="info-value">{{ $department->location ?? 'No especificada' }}</div>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="info-row">
                            <div class="info-label">Tipo de Mantenimiento:</div>
                            <div class="info-value">
                                @if($maintenance->maintenance_type == 'preventive')
                                    <span class="badge badge-success">Preventivo</span>
                                @elseif($maintenance->maintenance_type == 'corrective')
                                    <span class="badge badge-danger">Correctivo</span>
                                @elseif($maintenance->maintenance_type == 'predictive')
                                    <span class="badge badge-info">Predictivo</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Técnico Responsable:</div>
                            <div class="info-value">{{ $technician->name }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Cargo del Técnico:</div>
                            <div class="info-value">{{ $technician->position }}</div>
                        </div>
                        
                        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
                            @php
                                $additionalInfo = json_decode($maintenance->additional_info, true);
                            @endphp
                            
                            @if(isset($additionalInfo['priority']))
                                <div class="info-row">
                                    <div class="info-label">Prioridad:</div>
                                    <div class="info-value">
                                        @php
                                            $priorityLabel = 'No especificada';
                                            $priorityClass = '';
                                            
                                            switch($additionalInfo['priority']) {
                                                case 'low':
                                                    $priorityLabel = 'Baja';
                                                    $priorityClass = 'low';
                                                    break;
                                                case 'medium':
                                                    $priorityLabel = 'Media';
                                                    $priorityClass = 'medium';
                                                    break;
                                                case 'high':
                                                    $priorityLabel = 'Alta';
                                                    $priorityClass = 'high';
                                                    break;
                                                case 'critical':
                                                    $priorityLabel = 'Crítica';
                                                    $priorityClass = 'critical';
                                                    break;
                                            }
                                        @endphp
                                        <span class="priority-indicator priority-{{ $priorityClass }}"></span>
                                        {{ $priorityLabel }}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección 2: Información del Equipo -->
        <div class="section">
            <div class="section-title">2. INFORMACIÓN DEL EQUIPO</div>
            <div class="section-content">
                <div class="two-columns">
                    <div class="column">
                        <div class="info-row">
                            <div class="info-label">Nombre/ID:</div>
                            <div class="info-value">{{ $asset->name }} (ID: {{ $asset->id }})</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tipo de Equipo:</div>
                            <div class="info-value">{{ $asset->type }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Marca:</div>
                            <div class="info-value">{{ $asset->brand ?? 'No especificada' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Modelo:</div>
                            <div class="info-value">{{ $asset->model ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="info-row">
                            <div class="info-label">Número de Serie:</div>
                            <div class="info-value">{{ $asset->serial ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Código Patrimonial:</div>
                            <div class="info-value">{{ $asset->patrimony_code ?? 'No asignado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Estado Actual:</div>
                            <div class="info-value">
                                @if($asset->status == 'active')
                                    <span class="badge badge-success">Activo</span>
                                @elseif($asset->status == 'in_maintenance')
                                    <span class="badge badge-warning">En Mantenimiento</span>
                                @elseif($asset->status == 'inactive')
                                    <span class="badge badge-secondary">Inactivo</span>
                                @else
                                    <span class="badge badge-danger">De Baja</span>
                                @endif
                            </div>
                        </div>
                        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
                            @php
                                $additionalInfo = json_decode($maintenance->additional_info, true);
                            @endphp
                            
                            @if(isset($additionalInfo['estimated_time']))
                                <div class="info-row">
                                    <div class="info-label">Tiempo Estimado:</div>
                                    <div class="info-value">{{ $additionalInfo['estimated_time'] }} minutos</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección 3: Diagnóstico -->
        <div class="section">
            <div class="section-title">3. DIAGNÓSTICO</div>
            <div class="section-content">
                <div class="text-field">{!! $maintenance->diagnosis !!}</div>
            </div>
        </div>
        
        <!-- Sección 4: Procedimiento Realizado -->
        <div class="section">
            <div class="section-title">4. PROCEDIMIENTO REALIZADO</div>
            <div class="section-content">
                <div class="text-field">{!! $maintenance->procedure !!}</div>
            </div>
        </div>
        
        <!-- Sección 5: Materiales Utilizados (si existe) -->
        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
            @php
                $additionalInfo = json_decode($maintenance->additional_info, true);
            @endphp
            
            @if(isset($additionalInfo['materials_used']) && !empty($additionalInfo['materials_used']))
                <div class="section">
                    <div class="section-title">5. MATERIALES Y REPUESTOS UTILIZADOS</div>
                    <div class="section-content">
                        <div class="text-field">
                            {{ $additionalInfo['materials_used'] }}
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Sección 6: Recomendaciones (si existe) -->
        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
            @php
                $additionalInfo = json_decode($maintenance->additional_info, true);
            @endphp
            
            @if(isset($additionalInfo['recommendations']) && !empty($additionalInfo['recommendations']))
                <div class="section">
                    <div class="section-title">6. RECOMENDACIONES</div>
                    <div class="section-content">
                        <div class="recommendations-note">
                            {{ $additionalInfo['recommendations'] }}
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Sección 7: Observaciones Adicionales -->
        <div class="section">
            <div class="section-title">{{ isset($additionalInfo['recommendations']) ? '7' : '6' }}. OBSERVACIONES ADICIONALES</div>
            <div class="section-content">
                <div class="text-field">
                    <div>{{ $maintenance->start_date->format('d/m/Y H:i') }} - Inicio de mantenimiento</div>
                    @if($maintenance->end_date)
                        <div>{{ $maintenance->end_date->format('d/m/Y H:i') }} - Finalización de mantenimiento</div>
                    @else
                        <div>En proceso - Mantenimiento no finalizado</div>
                    @endif
                    
                    @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
                        @php
                            $additionalInfo = json_decode($maintenance->additional_info, true);
                        @endphp
                        
                        @if(isset($additionalInfo['next_maintenance_date']) && !empty($additionalInfo['next_maintenance_date']))
                            <div style="margin-top: 10px;"><strong>Próximo mantenimiento programado:</strong> {{ \Carbon\Carbon::parse($additionalInfo['next_maintenance_date'])->format('d/m/Y') }}
                                @if(isset($additionalInfo['next_maintenance_type']))
                                    - Tipo: 
                                    @if($additionalInfo['next_maintenance_type'] == 'preventive')
                                        <span class="badge badge-success">Preventivo</span>
                                    @elseif($additionalInfo['next_maintenance_type'] == 'corrective')
                                        <span class="badge badge-danger">Correctivo</span>
                                    @elseif($additionalInfo['next_maintenance_type'] == 'predictive')
                                        <span class="badge badge-info">Predictivo</span>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Imágenes del mantenimiento (si existen) -->
        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
            @php
                $additionalInfo = json_decode($maintenance->additional_info, true);
            @endphp
            
            @if(isset($additionalInfo['images']) && !empty($additionalInfo['images']))
                <div class="section">
                    <div class="section-title">{{ isset($additionalInfo['recommendations']) ? '8' : '7' }}. EVIDENCIA FOTOGRÁFICA</div>
                    <div class="section-content">
                        <div class="images-container">
                            @foreach($additionalInfo['images'] as $image)
                                <img src="{{ public_path('storage/' . $image) }}" class="maintenance-image" alt="Imagen de mantenimiento">
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Firmas -->
        <div class="section">
            <div class="section-title">{{ isset($additionalInfo['images']) ? '9' : (isset($additionalInfo['recommendations']) ? '8' : '7') }}. FIRMAS DE CONFORMIDAD</div>
            <div class="section-content">
                <div class="signatures">
                    <div class="signature-block">
                        @if($signatures && isset($technicianSignature))
                            <img src="data:image/png;base64,{{ $technicianSignature->digital_signature }}" class="signature-image">
                        @else
                            <div style="height: 60px;"></div>
                        @endif
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $technician->name }}</div>
                        <div class="signature-title">Técnico Responsable</div>
                        @if($signatures && isset($technicianSignature))
                            <div class="signature-date">Firmado el {{ $technicianSignature->signature_date->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                    
                    <div class="signature-block">
                        @if($signatures && isset($managerSignature))
                            <img src="data:image/png;base64,{{ $managerSignature->digital_signature }}" class="signature-image">
                        @else
                            <div style="height: 60px;"></div>
                        @endif
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $manager->name }}</div>
                        <div class="signature-title">Jefe de Departamento</div>
                        @if($signatures && isset($managerSignature))
                            <div class="signature-date">Firmado el {{ $managerSignature->signature_date->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
                
                @if($signatures)
                    <table class="signatures-table">
                        <thead>
                            <tr>
                                <th>Firmante</th>
                                <th>Cargo</th>
                                <th>Tipo de Firma</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificate->signatures as $signature)
                                <tr>
                                    <td>{{ $signature->user->name }}</td>
                                    <td>{{ $signature->user->position }}</td>
                                    <td>{{ $signature->signature_type == 'technician' ? 'Técnico' : 'Jefe de Departamento' }}</td>
                                    <td>{{ $signature->signature_date->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    
    <div class="footer">
        Este documento es de carácter oficial y fue generado por el Sistema de Gestión de Mantenimiento de Activos Tecnológicos.<br>
        Fecha de generación: {{ $date }} {{ $time }} | Código de verificación: {{ $certificate->code }}<br>
        Municipalidad Distrital de San Jerónimo - Oficina de Tecnologías de la Información
    </div>
</body>
</html>