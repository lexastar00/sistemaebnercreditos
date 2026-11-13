<?php

namespace Tests\Feature;

use Database\Seeders\CarreraSeeder;
use Database\Seeders\EstadoAtencionSeeder;
use Database\Seeders\RolDocumentoSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Comprueba que la base creada por las migraciones coincide con el diseño aprobado (Fase 5).
 */
class EstructuraBaseDatosTest extends TestCase
{
    use RefreshDatabase;

    public function test_existen_todas_las_tablas_del_diseno(): void
    {
        foreach (['carreras', 'departamentos', 'servicios', 'tipos_atencion', 'tipos_problema', 'estados_atencion', 'periodos_escolares', 'roles_documento', 'personal', 'personal_rol', 'activity_log', 'estudiantes', 'tramites_residencia', 'tramites_servicio_social', 'tramites_actividad_complementaria', 'tipos_documento', 'bloques_firma', 'folios_contador', 'documentos_generados', 'documento_firmante', 'folios_atencion', 'atenciones', 'atencion_adjuntos', 'atencion_seguimientos', 'users'] as $tabla) {
            $this->assertTrue(Schema::hasTable($tabla), "Falta la tabla {$tabla}");
        }
    }

    public function test_users_tiene_los_campos_de_vinculo_con_personal(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['personal_id', 'activo']));
    }

    public function test_la_atencion_registra_entrada_salida_y_tipo_de_solicitante(): void
    {
        $this->assertTrue(Schema::hasColumns('atenciones', ['fecha_hora', 'hora_salida', 'tipo_solicitante']));
    }

    public function test_no_se_puede_relacionar_un_estudiante_con_una_carrera_inexistente(): void
    {
        $this->expectException(QueryException::class);

        DB::table('estudiantes')->insert(['nombre_completo' => 'Prueba', 'matricula' => '000000001', 'carrera_id' => 9999]);
    }

    public function test_no_se_puede_repetir_la_matricula(): void
    {
        $this->seed(CarreraSeeder::class);
        $carreraId = DB::table('carreras')->value('id');

        DB::table('estudiantes')->insert(['nombre_completo' => 'Uno', 'matricula' => '202500001', 'carrera_id' => $carreraId]);

        $this->expectException(QueryException::class);
        DB::table('estudiantes')->insert(['nombre_completo' => 'Dos', 'matricula' => '202500001', 'carrera_id' => $carreraId]);
    }

    public function test_no_se_puede_duplicar_el_folio_de_una_atencion(): void
    {
        $this->seed(EstadoAtencionSeeder::class);

        $departamentoId = DB::table('departamentos')->insertGetId(['nombre' => 'Área de Sistemas']);
        $servicioId = DB::table('servicios')->insertGetId(['nombre' => 'Soporte', 'departamento_id' => $departamentoId]);
        $tipoAtencionId = DB::table('tipos_atencion')->insertGetId(['nombre' => 'Soporte']);
        $personalId = DB::table('personal')->insertGetId(['nombre_completo' => 'Persona de prueba']);
        $usuarioId = DB::table('users')->insertGetId(['name' => 'Prueba', 'email' => 'prueba@example.com', 'password' => 'x']);
        $estadoId = DB::table('estados_atencion')->value('id');

        $atencion = fn (string $folio) => [
            'folio' => $folio,
            'fecha_hora' => now(),
            'solicitante_nombre' => 'Solicitante',
            'asunto' => 'Prueba',
            'jefe_id' => $personalId,
            'secretario_id' => $personalId,
            'servicio_id' => $servicioId,
            'tipo_atencion_id' => $tipoAtencionId,
            'descripcion_problema' => 'Descripción',
            'estado_id' => $estadoId,
            'usuario_registro_id' => $usuarioId,
        ];

        DB::table('atenciones')->insert($atencion('SIS-2026-000001'));

        $this->expectException(QueryException::class);
        DB::table('atenciones')->insert($atencion('SIS-2026-000001'));
    }

    public function test_los_datos_iniciales_de_catalogos_son_los_esperados(): void
    {
        $this->seed([CarreraSeeder::class, EstadoAtencionSeeder::class, RolDocumentoSeeder::class, TipoDocumentoSeeder::class]);

        $this->assertSame(1, DB::table('carreras')->count());
        $this->assertSame(6, DB::table('estados_atencion')->count());
        $this->assertSame(8, DB::table('roles_documento')->count());
        $this->assertSame(5, DB::table('tipos_documento')->count());
        // 4 cartas con 2 bloques + la constancia con 4 bloques
        $this->assertSame(12, DB::table('bloques_firma')->count());
    }

    public function test_cada_tipo_de_documento_tiene_su_prefijo_de_folio(): void
    {
        $this->seed([RolDocumentoSeeder::class, TipoDocumentoSeeder::class]);

        $this->assertSame(
            ['CTA' => 'ISC-CTA', 'RES_ACEP' => 'ISC-RES-ACEP', 'RES_TERM' => 'ISC-RES-TERM', 'SS_ACEP' => 'ISC-SS-ACEP', 'SS_TERM' => 'ISC-SS-TERM'],
            DB::table('tipos_documento')->orderBy('clave')->pluck('prefijo_folio', 'clave')->all()
        );
    }
}
