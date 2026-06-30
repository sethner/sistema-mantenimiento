<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Mantenimientos - {{ date('Y') }}</title>
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
        .kpi-value { font-size: 14px; font-weight: 900; color: #0284c7; display: block; }
        .kpi-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-top: 4px; }

        /* LISTADO DE MANTENIMIENTOS */
        .entries-container { padding: 10px 40px; }
        .entry-card {
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .entry-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .entry-date { font-weight: 900; font-size: 10px; color: #0f172a; }
        .entry-type { 
            float: right;
            font-size: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            padding: 2px 8px;
            border-radius: 4px;
        }
        .type-preventivo { background-color: #dcfce7; color: #166534; }
        .type-correctivo { background-color: #fee2e2; color: #991b1b; }

        .entry-body { padding: 15px; }
        .entry-table { width: 100%; border-collapse: collapse; }
        .entry-table td { padding: 5px 0; vertical-align: top; }
        .field-label { 
            width: 120px; 
            font-size: 8px; 
            font-weight: 900; 
            color: #94a3b8; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .field-content { font-size: 10px; color: #334155; line-height: 1.5; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-blue { color: #0284c7; font-weight: bold; }

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
                    <p>HISTORIAL TÉCNICO</p>
                    <h1>BITÁCORA DE SOPORTE</h1>
                    <p>PERIODO ANUAL {{ date('Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="info-column">
                    <span class="info-label">Identificación del Activo</span>
                    <div class="info-value">
                        @if($equipoSeleccionado)
                            <strong>EQUIPO: {{ strtoupper($equipoSeleccionado->nombre) }}</strong><br>
                            CÓDIGO: {{ $equipoSeleccionado->codigo }}<br>
                            MARCA/MODELO: {{ $equipoSeleccionado->marca ?? 'S/M' }} / {{ $equipoSeleccionado->modelo ?? 'S/M' }}<br>
                            ESTADO ACTUAL: <span style="color: {{ $equipoSeleccionado->estado == 'con_falla' ? '#dc2626' : ($equipoSeleccionado->estado == 'en_mantenimiento' ? '#d97706' : '#16a34a') }}; font-weight: bold;">{{ strtoupper(str_replace('_', ' ', $equipoSeleccionado->estado)) }}</span>
                        @else
                            <strong>REPORTE GENERAL DE SOPORTE</strong><br>
                            Consolidado Multidispositivo<br>
                            Institución: {{ strtoupper($config->nombre_institucion) }}<br>
                            Dirección: {{ $config->direccion }}<br>
                            RUC: {{ $config->ruc }}
                        @endif
                    </div>
                </td>
                <td class="info-column">
                    <span class="info-label">Resumen de Gestión</span>
                    <div class="info-value">
                        Total Intervenciones: {{ $estadisticas['total'] }} sesiones<br>
                        Inversión Ejecutada: <span class="text-blue">S/ {{ number_format($estadisticas['costo_total'], 2) }}</span><br>
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
                    <span class="kpi-value">{{ $estadisticas['total'] }}</span>
                    <span class="kpi-label">Total Sesiones</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value" style="color: #16a34a;">{{ $estadisticas['preventivos'] }}</span>
                    <span class="kpi-label">Maint. Preventivo</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value" style="color: #dc2626;">{{ $estadisticas['correctivos'] }}</span>
                    <span class="kpi-label">Maint. Correctivo</span>
                </td>
                <td class="kpi-box" style="border-radius: 0 8px 8px 0; background-color: #f0f9ff;">
                    <span class="kpi-value">S/ {{ number_format($estadisticas['costo_total'], 2) }}</span>
                    <span class="kpi-label">Costo Operativo</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- LISTADO DE INTERVENCIONES -->
    <div class="entries-container">
        <span class="info-label" style="margin-bottom: 15px;">Registro Cronológico de Intervenciones</span>
        
        @forelse($mantenimientos as $m)
            <div class="entry-card">
                <div class="entry-header">
                    <span class="entry-date">{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</span>
                    <span class="entry-type {{ $m->tipo == 'preventivo' ? 'type-preventivo' : 'type-correctivo' }}">
                        {{ strtoupper($m->tipo) }}
                    </span>
                    <span style="font-size: 9px; color: #64748b; margin-left: 15px;">
                        TÉCNICO: {{ strtoupper($m->usuario->name ?? 'SISTEMA') }}
                    </span>
                </div>
                <div class="entry-body">
                    <table class="entry-table">
                        @if(!$equipoSeleccionado)
                        <tr>
                            <td class="field-label">Dispositivo:</td>
                            <td class="field-content">
                                <strong>[{{ $m->equipo->codigo }}]</strong> {{ strtoupper($m->equipo->nombre) }}
                                @if($m->equipo->estado == 'con_falla')
                                    <span style="color: #dc2626; font-weight: bold; font-size: 8px; margin-left: 5px;">(ESTADO: CON FALLA)</span>
                                @elseif($m->equipo->estado == 'en_mantenimiento')
                                    <span style="color: #d97706; font-weight: bold; font-size: 8px; margin-left: 5px;">(ESTADO: EN PROCESO DE MANTENIMIENTO)</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="field-label">Hallazgos:</td>
                            <td class="field-content">{{ $m->diagnostico ?: 'Mantenimiento de rutina sin anomalías detectadas.' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Solución:</td>
                            <td class="field-content">{{ $m->accion ?: 'Acciones de soporte técnico ejecutadas según protocolo.' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Inversión:</td>
                            <td class="field-content font-bold">S/ {{ number_format($m->costo, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px; background-color: #f8fafc; border-radius: 8px; color: #94a3b8;">
                No se registran intervenciones técnicas en este periodo.
            </div>
        @endforelse
    </div>

    <div class="page-footer">
        Bitácora de Soporte Técnico AIP | Institución Educativa Jorge Chávez | Generado el {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
