<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Mantenimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .code {
            font-size: 14px;
            font-weight: bold;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .text-field {
            margin-top: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
            min-height: 50px;
        }
        .signatures {
            margin-top: 50px;
        }
        .signature-block {
            display: inline-block;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin: 10px 0;
            border-top: 1px solid #000;
            height: 0;
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-title {
            font-size: 12px;
            color: #555;
        }
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">ACTA DE MANTENIMIENTO DE EQUIPOS TECNOLÓGICOS</div>
            <div class="code">Código: {{ $certificate->code }}</div>
        </div>
        
        <div class="section">
            <div class="section-title">1. INFORMACIÓN GENERAL</div>
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ date('d/m/Y', strtotime($maintenance->start_date)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Departamento:</div>
                <div class="info-value">{{ $department->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Responsable Técnico:</div>
                <div class="info-value">{{ $technician->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cargo:</div>
                <div class="info-value">{{ $technician->position }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Mantenimiento:</div>
                <div class="info-value">
                    @if($maintenance->maintenance_type == 'preventive')
                        Preventivo
                    @elseif($maintenance->maintenance_type == 'corrective')
                        Correctivo
                    @elseif($maintenance->maintenance_type == 'predictive')
                        Predictivo
                    @endif
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">2. INFORMACIÓN DEL EQUIPO</div>
            <div class="info-row">
                <div class="info-label">Nombre/ID:</div>
                <div class="info-value">{{ $asset->name }} (ID: {{ $asset->id }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo:</div>
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
            <div class="info-row">
                <div class="info-label">Serial:</div>
                <div class="info-value">{{ $asset->serial ?? 'No especificado' }}</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">3. DIAGNÓSTICO</div>
            <div class="text-field">{{ $maintenance->diagnosis }}</div>
        </div>
        
        <div class="section">
            <div class="section-title">4. PROCEDIMIENTO REALIZADO</div>
            <div class="text-field">{{ $maintenance->procedure }}</div>
        </div>
        
        <div class="section">
            <div class="section-title">5. OBSERVACIONES ADICIONALES</div>
            <div class="text-field">
                {{ $maintenance->start_date->format('d/m/Y H:i') }} - Inicio de mantenimiento<br>
                {{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') . ' - Finalización de mantenimiento' : 'En proceso' }}
            </div>
        </div>
        
        <div class="signatures">
            <div class="signature-block" style="float: left;">
                @if($signatures && isset($technicianSignature))
                    <img src="data:image/png;base64,{{ $technicianSignature->digital_signature }}" class="signature-image">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $technician->name }}</div>
                <div class="signature-title">Técnico Responsable</div>
            </div>
            
            <div class="signature-block" style="float: right;">
                @if($signatures && isset($managerSignature))
                    <img src="data:image/png;base64,{{ $managerSignature->digital_signature }}" class="signature-image">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $manager->name }}</div>
                <div class="signature-title">Jefe de Departamento</div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        Este documento es de carácter oficial y fue generado por el Sistema de Gestión de Mantenimiento de Activos Tecnológicos.<br>
        Fecha de generación: {{ $date }} {{ $time }} | Código: {{ $certificate->code }}
    </div>
</body>
</html>