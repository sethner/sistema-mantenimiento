<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggest_diagnosis_requires_description_and_groq_key(): void
    {
        $admin = $this->userWithRole('administrador');

        // Without key
        config(['services.groq.key' => '']);
        $this->actingAs($admin)
            ->postJson(route('ai.diagnosticar'), ['descripcion' => 'Mi equipo no enciende'])
            ->assertStatus(500)
            ->assertJsonPath('error', 'API Key de IA no configurada.');

        // With key, without description
        config(['services.groq.key' => 'gsk_key']);
        $this->actingAs($admin)
            ->postJson(route('ai.diagnosticar'), ['descripcion' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('descripcion');
    }

    public function test_suggest_diagnosis_success_with_groq_mock(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => "Diagnóstico probable:\n- Memoria RAM defectuosa\nNivel de urgencia: Alto\nPosibles soluciones:\n- Reemplazar RAM"
                        ]
                    ]
                ]
            ], 200)
        ]);

        config(['services.groq.key' => 'gsk_key']);
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->postJson(route('ai.diagnosticar'), [
                'descripcion' => 'Pantalla azul de la muerte constante',
            ]);

        $response->assertOk()
            ->assertJsonPath('diagnostico', "Diagnóstico probable:\n- Memoria RAM defectuosa\nNivel de urgencia: Alto\nPosibles soluciones:\n- Reemplazar RAM");

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions' &&
                $request->hasHeader('Authorization', 'Bearer gsk_key');
        });
    }

    public function test_suggest_diagnosis_handles_groq_api_failure(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([], 502)
        ]);

        config(['services.groq.key' => 'gsk_key']);
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->postJson(route('ai.diagnosticar'), [
                'descripcion' => 'Pantalla azul de la muerte constante',
            ]);

        $response->assertStatus(502)
            ->assertJsonPath('error', 'Error al conectar con el servicio de IA: 502');
    }

    public function test_predictive_analysis_success_with_groq_mock(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => "Análisis predictivo de fallas futuras."
                        ]
                    ]
                ]
            ], 200)
        ]);

        config(['services.groq.key' => 'gsk_key']);
        $admin = $this->userWithRole('administrador');
        $tipo = \App\Models\TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-701',
            'nombre' => 'PC Laboratorio',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        // Add dummy maintenance & fallas
        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $admin->id,
            'tipo' => 'correctivo',
            'descripcion' => 'Fallo disco',
            'fecha' => '2026-06-01',
            'estado' => 'finalizado',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('ai.predictivo', $equipo));

        $response->assertOk()
            ->assertJsonPath('analisis', "Análisis predictivo de fallas futuras.");
    }
}
