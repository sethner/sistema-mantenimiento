<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$componentes = App\Models\Componente::with('tipo')->get();
$agrupados = $componentes->groupBy(function($c) {
    return $c->tipo->nombre ?? \App\Models\TipoEquipo::where('id', $c->tipo_id)->value('nombre') ?? 'Sin Tipo';
});

echo "Grupos: \n";
print_r(array_keys($agrupados->toArray()));

if (isset($agrupados['PC'])) {
    $grupo = $agrupados['PC'];
    $tipoModel = $grupo->first()->tipo;
    echo "Tipo is " . ($tipoModel ? "NOT NULL" : "NULL") . "\n";
    if (!$tipoModel) {
        $tipoFallback = \App\Models\TipoEquipo::find($grupo->first()->tipo_id);
        echo "Fallback Tipo is " . ($tipoFallback ? "NOT NULL. Imagen: " . $tipoFallback->imagen : "NULL") . "\n";
    }
}
