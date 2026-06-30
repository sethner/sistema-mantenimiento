<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Ejecutivo - {{ date('Y') }}</title>
    <style>
        @page { margin: 0px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #334155; 
            margin: 0; 
            padding: 0; 
            background-color: #ffffff;
        }
        
        /* HEADER PRINCIPAL (Estilo Corporativo) */
        .header-bar { 
            background-color: #ffffff; 
            border-top: 6px solid #0284c7;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 40px; 
            margin: 0;
            overflow: hidden;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-box { 
            width: 40%; 
            vertical-align: middle;
            color: #0f172a;
            font-weight: 900;
        }
        .logo-img { max-height: 50px; }
        .header-title-box { 
            width: 60%; 
            text-align: right; 
            vertical-align: middle;
        }
        .header-title-box p:first-child { 
            margin: 0 0 3px; 
            font-size: 9px; 
            font-weight: bold; 
            color: #64748b; 
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header-title-box h1 { 
            margin: 0; 
            font-size: 18px; 
            font-weight: 800; 
            color: #0284c7; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .header-title-box p:last-child { 
            margin: 4px 0 0; 
            font-size: 8px; 
            color: #94a3b8; 
            letter-spacing: 1px;
        }

        /* INFORMACIÓN DE SECCIÓN */
        .info-container { padding: 30px 40px 10px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-column { width: 50%; vertical-align: top; padding-right: 20px; }
        .info-label { 
            font-size: 9px; 
            font-weight: 900; 
            color: #000; 
            text-transform: uppercase; 
            margin-bottom: 5px;
            display: block;
        }
        .info-value { 
            font-size: 10px; 
            color: #475569; 
            margin-bottom: 15px; 
            line-height: 1.4;
        }

        /* TABLA DE DATOS */
        .table-container { padding: 10px 40px; }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table thead th { 
            background-color: #0284c7; 
            color: white; 
            text-align: left; 
            padding: 10px 12px; 
            font-size: 9px; 
            text-transform: uppercase;
            font-weight: bold;
            border: none;
        }
        .data-table tbody td { 
            padding: 12px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 10px;
        }
        .data-table tbody tr:nth-child(even) { background-color: #f8fafc; }

        /* RESUMEN DE INDICADORES (KPIs) */
        .kpi-container { padding: 20px 40px; }
        .kpi-table { width: 100%; border-collapse: collapse; }
        .kpi-box { 
            width: 25%; 
            padding: 15px; 
            background-color: #f8fafc; 
            border: 1px solid #f1f5f9;
            text-align: center;
        }
        .kpi-value { font-size: 16px; font-weight: 900; color: #0284c7; display: block; }
        .kpi-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-top: 4px; }

        /* TOTALES Y CONCLUSIÓN */
        .footer-container { padding: 20px 40px; }
        .conclusion-box { 
            background-color: #f0f9ff; 
            border-left: 4px solid #0284c7; 
            padding: 15px; 
            margin-bottom: 30px;
        }
        .conclusion-box strong { color: #0369a1; display: block; margin-bottom: 5px; }

        /* FIRMAS */
        .signature-table { width: 100%; margin-top: 50px; }
        .signature-box { width: 100%; text-align: center; }
        .signature-line { 
            border-top: 1px solid #cbd5e1; 
            width: 200px; 
            margin: 0 auto 5px; 
            padding-top: 10px;
            font-weight: bold;
            font-size: 10px;
            color: #1e293b;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #0284c7; font-weight: 900; }

        .page-footer { 
            position: fixed; 
            bottom: 20px; 
            left: 40px; 
            right: 40px; 
            font-size: 8px; 
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header-bar">
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    @if($config->logo_base64)
                        <img src="{{ $config->logo_base64 }}" class="logo-img">
                    @else
                        <span style="font-size: 24px; font-weight: 900;">SISTEMA AIP</span>
                    @endif
                </td>
                <td class="header-title-box">
                    <p>RESUMEN DE GESTIÓN TÉCNICA</p>
                    <h1>REPORTE EJECUTIVO</h1>
                    <p>ID: REP-{{ date('Ymd') }}-AIP</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="info-column">
                    <span class="info-label">Información Institucional</span>
                    <div class="info-value">
                        <strong>{{ strtoupper($config->nombre_institucion) }}</strong><br>
                        Aula de Innovación Pedagógica<br>
                        Director: {{ $config->director_nombre }}<br>
                        Dirección: {{ $config->direccion }}<br>
                        RUC: {{ $config->ruc }}
                    </div>
                </td>
                <td class="info-column">
                    <span class="info-label">Detalles del Reporte</span>
                    <div class="info-value">
                        Fecha de Emisión: {{ date('d/m/Y') }}<br>
                        Periodo: Anual {{ date('Y') }}<br>
                        Generado por: Sistema de Gestión Técnica
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- INDICADORES CLAVE -->
    <div class="kpi-container">
        <table class="kpi-table" style="width: 100%;">
            <tr>
                <td class="kpi-box" style="border-radius: 8px 0 0 8px;">
                    <span class="kpi-value">{{ $totalEquipos }}</span>
                    <span class="kpi-label">Activos Totales</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value" style="color: #16a34a;">{{ $equiposOperativos }}</span>
                    <span class="kpi-label">Operativos</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value" style="color: #dc2626;">{{ $equiposFalla }}</span>
                    <span class="kpi-label">Con Incidencias</span>
                </td>
                <td class="kpi-box" style="border-radius: 0 8px 8px 0; background-color: #f0f9ff;">
                    <span class="kpi-value">S/ {{ number_format($totalCosto, 2) }}</span>
                    <span class="kpi-label">Inversión Ejecutada</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- TABLA DE BIENES -->
    <div class="table-container">
        <span class="info-label">Distribución de Activos por Categoría</span>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Tipo de Dispositivo</th>
                    <th class="text-center">Cant. Unidades</th>
                    <th class="text-center">Porcentaje (%)</th>
                    <th class="text-right">Inversión Real</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equiposPorTipo as $tipo => $datos)
                <tr>
                    <td class="font-bold">{{ strtoupper($tipo) }}</td>
                    <td class="text-center">{{ $datos['cantidad'] }}</td>
                    <td class="text-center">{{ $totalEquipos > 0 ? number_format(($datos['cantidad'] / $totalEquipos) * 100, 1) : '0.0' }}%</td>
                    <td class="text-right">S/ {{ number_format($datos['costo'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- EQUIPOS CON INCIDENCIAS / FALLAS -->
    <div class="table-container">
        <span class="info-label" style="color: #dc2626;">Equipos Reportados con Falla (Fuera de Servicio)</span>
        @if($equiposCriticos->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%; background-color: #dc2626;">Código</th>
                        <th style="width: 45%; background-color: #dc2626;">Nombre del Equipo</th>
                        <th style="width: 30%; background-color: #dc2626;">Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equiposCriticos as $eq)
                    <tr>
                        <td class="font-bold font-mono">{{ $eq->codigo }}</td>
                        <td>{{ strtoupper($eq->nombre) }}</td>
                        <td>{{ strtoupper($eq->tipo->nombre ?? 'OTRO') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="conclusion-box" style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 15px; margin-bottom: 20px;">
                <strong style="color: #15803d; display: block; margin-bottom: 5px;">Sin incidencias activas:</strong>
                No se registran equipos con fallas en este periodo.
            </div>
        @endif
    </div>

    <!-- CONCLUSIÓN Y FIRMA -->
    <div class="footer-container">
        <div class="conclusion-box">
            <strong>Resumen de Operatividad:</strong>
            El estado de operatividad tecnológica de la institución se encuentra en un <span class="text-blue">{{ $totalEquipos > 0 ? number_format(($equiposOperativos / $totalEquipos) * 100, 1) : '0.0' }}%</span>. Se recomienda continuar con el programa de mantenimiento preventivo trimestral para garantizar la continuidad del servicio educativo.
        </div>

        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-line">
                        {{ strtoupper($config->director_nombre) }}
                    </div>
                    <span style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Director(a) General</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer">
        Generado automáticamente por el Sistema de Gestión AIP | Documento Original de Auditoría Técnica | {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
