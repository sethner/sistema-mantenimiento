@php
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $periodo = $estadisticas['anio'] . ($estadisticas['mes'] ? ' - ' . $meses[$estadisticas['mes']] : ' - Todo el año');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inversión - {{ $periodo }}</title>
    <style>
        @page { margin: 0px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #334155; 
            margin: 0; 
            padding: 0; 
            background-color: #ffffff;
        }
        
        /* HEADER PRINCIPAL (Estilo Corporativo) */
        .header-bar { 
            background-color: #ffffff; 
            border-top: 6px solid #059669;
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
            color: #059669; 
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

        /* MÉTRICAS RÁPIDAS (KPIs) */
        .kpi-container { padding: 0 40px 20px; }
        .kpi-table { width: 100%; border-collapse: collapse; }
        .kpi-box { 
            width: 25%; 
            padding: 12px; 
            background-color: #f8fafc; 
            border: 1px solid #f1f5f9;
            text-align: center;
        }
        .kpi-value { font-size: 14px; font-weight: 900; color: #059669; display: block; }
        .kpi-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-top: 4px; }

        /* LISTADO DE MANTENIMIENTOS */
        .entries-container { padding: 10px 40px; }
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th { 
            background-color: #f1f5f9; 
            color: #475569; 
            font-size: 9px; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-align: left; 
            padding: 10px; 
            border-bottom: 2px solid #e2e8f0; 
        }
        .table-data td { 
            padding: 10px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 10px; 
            vertical-align: top; 
        }
        .type-badge { 
            font-size: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            padding: 3px 6px;
            border-radius: 4px;
        }
        .type-preventivo { background-color: #dcfce7; color: #166534; }
        .type-correctivo { background-color: #fee2e2; color: #991b1b; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-emerald { color: #059669; font-weight: bold; }
        .font-bold { font-weight: bold; }

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
                        <span style="font-size: 20px; font-weight: 900;">SISTEMA AIP</span>
                    @endif
                </td>
                <td class="header-title-box">
                    <p>REPORTE FINANCIERO</p>
                    <h1>INVERSIÓN TOTAL</h1>
                    <p>PERIODO: {{ strtoupper($periodo) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="info-column">
                    <span class="info-label">Datos de la Institución</span>
                    <div class="info-value">
                        <strong>{{ strtoupper($config->nombre_institucion) }}</strong><br>
                        Dirección: {{ $config->direccion }}<br>
                        RUC: {{ $config->ruc }}
                    </div>
                </td>
                <td class="info-column">
                    <span class="info-label">Resumen de Periodo</span>
                    <div class="info-value">
                        Filtro aplicado: {{ $periodo }}<br>
                        Mantenimientos facturados: {{ $estadisticas['total_mantenimientos'] }}<br>
                        Fecha de Emisión: {{ date('d/m/Y H:i') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- MÉTRICAS RÁPIDAS -->
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-box" style="border-radius: 8px 0 0 8px;">
                    <span class="kpi-value">S/ {{ number_format($estadisticas['costo_preventivos'], 2) }}</span>
                    <span class="kpi-label">Inv. Preventiva ({{ $estadisticas['preventivos_count'] }})</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value" style="color: #dc2626;">S/ {{ number_format($estadisticas['costo_correctivos'], 2) }}</span>
                    <span class="kpi-label">Inv. Correctiva ({{ $estadisticas['correctivos_count'] }})</span>
                </td>
                <td class="kpi-box" style="border-radius: 0 8px 8px 0; background-color: #ecfdf5; border-color: #d1fae5; width: 50%;">
                    <span class="kpi-value" style="font-size: 18px;">S/ {{ number_format($estadisticas['costo_total'], 2) }}</span>
                    <span class="kpi-label" style="color: #059669;">INVERSIÓN TOTAL DEL PERIODO</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- LISTADO DE INTERVENCIONES -->
    <div class="entries-container">
        <span class="info-label" style="margin-bottom: 10px;">Desglose de Mantenimientos</span>
        
        <table class="table-data">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Equipo</th>
                    <th>Tipo</th>
                    <th>Técnico</th>
                    <th class="text-right">Costo (S/)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimientos as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                    <td>
                        <strong>[{{ $m->equipo->codigo }}]</strong><br>
                        {{ $m->equipo->nombre }}
                        @if($m->equipo->estado == 'con_falla')
                            <br><span style="color: #dc2626; font-weight: bold; font-size: 8px;">(CON FALLA)</span>
                        @elseif($m->equipo->estado == 'en_mantenimiento')
                            <br><span style="color: #d97706; font-weight: bold; font-size: 8px;">(EN MANTENIMIENTO)</span>
                        @endif
                    </td>
                    <td>
                        <span class="type-badge {{ $m->tipo == 'preventivo' ? 'type-preventivo' : 'type-correctivo' }}">
                            {{ strtoupper($m->tipo) }}
                        </span>
                    </td>
                    <td>{{ $m->usuario->name ?? 'N/A' }}</td>
                    <td class="text-right font-bold text-emerald">
                        {{ number_format($m->costo, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px; color: #94a3b8;">
                        No se encontraron registros de inversión en el periodo seleccionado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-footer">
        Reporte de Inversión Tecnológica | Sistema AIP | Generado el {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
