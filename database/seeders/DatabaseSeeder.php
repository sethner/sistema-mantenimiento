<?php

namespace Database\Seeders;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\Configuracion;
use App\Models\Equipo;
use App\Models\HistorialFalla;
use App\Models\Mantenimiento;
use App\Models\Notificacion;
use App\Models\Role;
use App\Models\TipoEquipo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles del Sistema
        $adminRole = $this->role('administrador');
        $tecnicoRole = $this->role('tecnico');
        $supervisorRole = $this->role('supervisor');

        // 2. Usuarios de Prueba Predeterminados
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('12345678'),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@gmail.com'],
            [
                'name' => 'Técnico de Soporte',
                'password' => Hash::make('12345678'),
            ]
        );
        $tecnico->roles()->sync([$tecnicoRole->id]);

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@gmail.com'],
            [
                'name' => 'Supervisor de Innovación',
                'password' => Hash::make('12345678'),
            ]
        );
        $supervisor->roles()->sync([$supervisorRole->id]);

        // 3. Tipos de Equipos
        $pcType = TipoEquipo::firstOrCreate(['nombre' => 'PC']);
        $laptopType = TipoEquipo::firstOrCreate(['nombre' => 'Laptop']);
        $printerType = TipoEquipo::firstOrCreate(['nombre' => 'Impresora']);
        $projectorType = TipoEquipo::firstOrCreate(['nombre' => 'Proyector']);

        // 4. Categorías de Componentes
        $interno = CategoriaComponente::firstOrCreate(['nombre' => 'Interno']);
        $periferico = CategoriaComponente::firstOrCreate(['nombre' => 'Periferico']);
        $consumible = CategoriaComponente::firstOrCreate(['nombre' => 'Consumible']);

        // 5. Catálogo de Componentes por Tipo de Equipo
        // Componentes para PC
        $ramPC = Componente::firstOrCreate([
            'tipo_id' => $pcType->id,
            'nombre' => 'Memoria RAM DDR4 8GB',
            'categoria_id' => $interno->id
        ]);
        $ssdPC = Componente::firstOrCreate([
            'tipo_id' => $pcType->id,
            'nombre' => 'Disco Duro SSD 480GB',
            'categoria_id' => $interno->id
        ]);
        $fuentePC = Componente::firstOrCreate([
            'tipo_id' => $pcType->id,
            'nombre' => 'Fuente de Alimentación 500W',
            'categoria_id' => $interno->id
        ]);
        $placaPC = Componente::firstOrCreate([
            'tipo_id' => $pcType->id,
            'nombre' => 'Placa Madre H410',
            'categoria_id' => $interno->id
        ]);

        // Componentes para Laptop
        $ramLaptop = Componente::firstOrCreate([
            'tipo_id' => $laptopType->id,
            'nombre' => 'Memoria RAM DDR4 16GB Laptop',
            'categoria_id' => $interno->id
        ]);
        $bateriaLaptop = Componente::firstOrCreate([
            'tipo_id' => $laptopType->id,
            'nombre' => 'Batería de Iones de Litio 3 Celdas',
            'categoria_id' => $interno->id
        ]);
        $tecladoLaptop = Componente::firstOrCreate([
            'tipo_id' => $laptopType->id,
            'nombre' => 'Teclado Español Laptop',
            'categoria_id' => $interno->id
        ]);

        // Componentes para Impresora
        $tonerImpresora = Componente::firstOrCreate([
            'tipo_id' => $printerType->id,
            'nombre' => 'Cartucho de Tóner Negro 105A',
            'categoria_id' => $consumible->id
        ]);
        $fusorImpresora = Componente::firstOrCreate([
            'tipo_id' => $printerType->id,
            'nombre' => 'Unidad Fusora 220V',
            'categoria_id' => $interno->id
        ]);

        // Componentes para Proyector
        $lamparaProyector = Componente::firstOrCreate([
            'tipo_id' => $projectorType->id,
            'nombre' => 'Lámpara de Reemplazo UHE 200W',
            'categoria_id' => $consumible->id
        ]);
        $filtroProyector = Componente::firstOrCreate([
            'tipo_id' => $projectorType->id,
            'nombre' => 'Filtro de Aire Antipolvo',
            'categoria_id' => $consumible->id
        ]);

        // 6. Equipos Tecnológicos
        // PC Laboratorio 1 (Operativo)
        $pc1 = Equipo::firstOrCreate(
            ['codigo' => 'AIP-PC-001'],
            [
                'nombre' => 'PC Laboratorio 01',
                'tipo_id' => $pcType->id,
                'marca' => 'Dell',
                'modelo' => 'OptiPlex 3080',
                'estado' => 'operativo',
                'frecuencia_mantenimiento' => 6,
                'proximo_mantenimiento' => now()->addMonths(6),
            ]
        );
        $pc1->componentes()->syncWithoutDetaching([
            $ramPC->id => ['estado' => 'bueno'],
            $ssdPC->id => ['estado' => 'bueno'],
            $fuentePC->id => ['estado' => 'bueno'],
            $placaPC->id => ['estado' => 'bueno'],
        ]);

        // PC Laboratorio 2 (Con Falla)
        $pc2 = Equipo::firstOrCreate(
            ['codigo' => 'AIP-PC-002'],
            [
                'nombre' => 'PC Laboratorio 02',
                'tipo_id' => $pcType->id,
                'marca' => 'Lenovo',
                'modelo' => 'ThinkCentre M70s',
                'estado' => 'con_falla',
                'frecuencia_mantenimiento' => 6,
                'proximo_mantenimiento' => now()->addMonths(6),
            ]
        );
        $pc2->componentes()->syncWithoutDetaching([
            $ramPC->id => ['estado' => 'malo'], // RAM dañada
            $ssdPC->id => ['estado' => 'bueno'],
            $fuentePC->id => ['estado' => 'regular'],
            $placaPC->id => ['estado' => 'bueno'],
        ]);

        // Laptop Docente (En Mantenimiento)
        $laptop1 = Equipo::firstOrCreate(
            ['codigo' => 'AIP-LP-001'],
            [
                'nombre' => 'Laptop Docente Primaria',
                'tipo_id' => $laptopType->id,
                'marca' => 'HP',
                'modelo' => 'ProBook 440 G8',
                'estado' => 'en_mantenimiento',
                'frecuencia_mantenimiento' => 4,
                'proximo_mantenimiento' => now()->addMonths(4),
            ]
        );
        $laptop1->componentes()->syncWithoutDetaching([
            $ramLaptop->id => ['estado' => 'bueno'],
            $bateriaLaptop->id => ['estado' => 'regular'],
            $tecladoLaptop->id => ['estado' => 'bueno'],
        ]);

        // Impresora AIP (Operativo)
        $impresora1 = Equipo::firstOrCreate(
            ['codigo' => 'AIP-IMP-001'],
            [
                'nombre' => 'Impresora Administración',
                'tipo_id' => $printerType->id,
                'marca' => 'HP',
                'modelo' => 'LaserJet Pro M404dn',
                'estado' => 'operativo',
                'frecuencia_mantenimiento' => 12,
                'proximo_mantenimiento' => now()->addMonths(12),
            ]
        );
        $impresora1->componentes()->syncWithoutDetaching([
            $tonerImpresora->id => ['estado' => 'bueno'],
            $fusorImpresora->id => ['estado' => 'bueno'],
        ]);

        // Proyector (Dado de Baja)
        $proyector1 = Equipo::firstOrCreate(
            ['codigo' => 'AIP-PROY-001'],
            [
                'nombre' => 'Proyector Aula 2A',
                'tipo_id' => $projectorType->id,
                'marca' => 'Epson',
                'modelo' => 'PowerLite S41+',
                'estado' => 'dado_de_baja',
                'frecuencia_mantenimiento' => 6,
                'proximo_mantenimiento' => null,
            ]
        );
        $proyector1->componentes()->syncWithoutDetaching([
            $lamparaProyector->id => ['estado' => 'malo'],
            $filtroProyector->id => ['estado' => 'malo'],
        ]);

        // 7. Historial de Mantenimientos
        // Mantenimiento finalizado para la PC Laboratorio 1 (Preventivo de rutina)
        Mantenimiento::firstOrCreate(
            ['descripcion' => 'Mantenimiento preventivo semestral de rutina - PC 01'],
            [
                'equipo_id' => $pc1->id,
                'user_id' => $tecnico->id,
                'tipo' => 'preventivo',
                'diagnostico' => 'Equipo funcionando normalmente, acumulación leve de polvo en ventilador.',
                'accion' => 'Limpieza interna completa y soplado de ventilador de CPU.',
                'fecha' => now()->subMonths(3),
                'proxima_fecha' => now()->addMonths(3),
                'estado' => 'finalizado',
                'costo' => 35.00,
            ]
        );

        // Mantenimiento pendiente para la PC Laboratorio 2 (Correctivo)
        $maintCorrectivo = Mantenimiento::firstOrCreate(
            ['descripcion' => 'Revisión por fallas continuas de pantalla azul (BSOD)'],
            [
                'equipo_id' => $pc2->id,
                'user_id' => $tecnico->id,
                'tipo' => 'correctivo',
                'fecha' => now()->subDays(1),
                'estado' => 'pendiente',
                'costo' => 0.00,
            ]
        );

        // Mantenimiento en proceso para la Laptop Docente (Correctivo de teclado/batería)
        Mantenimiento::firstOrCreate(
            ['descripcion' => 'El teclado tiene teclas duras y la batería dura muy poco'],
            [
                'equipo_id' => $laptop1->id,
                'user_id' => $tecnico->id,
                'tipo' => 'correctivo',
                'diagnostico' => 'Se verificó suciedad debajo del teclado y degradación de la batería al 40%.',
                'accion' => 'En espera de repuesto de batería nueva. Limpieza física de teclado realizada.',
                'fecha' => now(),
                'estado' => 'en_proceso',
                'costo' => 120.00,
            ]
        );

        // 8. Historial de Fallas Críticas
        HistorialFalla::firstOrCreate(
            [
                'equipo_id' => $pc2->id,
                'mantenimiento_id' => $maintCorrectivo->id,
                'componente_id' => $ramPC->id
            ],
            [
                'descripcion' => 'Falla crítica detectada en Memoria RAM DDR4 8GB (Malo)',
                'tipo' => 'correctivo',
                'fecha' => now()->subDays(1)
            ]
        );

        // 9. Buzón de Notificaciones de Prueba
        Notificacion::firstOrCreate(
            [
                'user_id' => $tecnico->id,
                'titulo' => 'Mantenimiento Asignado',
                'mensaje' => 'Se te ha asignado una nueva orden correctiva para la PC Laboratorio 02.'
            ],
            [
                'tipo' => 'asignacion',
                'enlace' => '/mantenimientos/' . $maintCorrectivo->id,
                'leida' => false,
            ]
        );

        // 10. Configuración Institucional Global
        Configuracion::firstOrCreate(
            ['ruc' => '20131321521'],
            [
                'nombre_institucion' => 'AULA DE INNOVACIÓN PEDAGÓGICA - AIP CENTRAL',
                'director_nombre' => 'MG. CARLOS SÁNCHEZ MEDINA',
                'direccion' => 'Av. Universitaria 1230, Los Olivos, Lima',
                'telefono' => '(01) 485-9652',
                'email' => 'soporte.aip@instituto.edu.pe',
                'logo_path' => null,
            ]
        );
    }

    private function role(string $nombre): Role
    {
        $role = Role::where('nombre', $nombre)->first();
        if (! $role) {
            $role = new Role();
            $role->nombre = $nombre;
            $role->slug = $this->slug($nombre);
            $role->save();
        }

        return $role;
    }

    private function slug(string $text): string
    {
        return str_replace(' ', '-', strtolower(trim($text)));
    }
}
