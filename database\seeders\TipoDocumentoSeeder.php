<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Los 5 documentos oficiales, su prefijo de folio y los bloques de firma o destinatario de cada uno.
 * Las plantillas Word se guardan en resources/plantillas.
 */
class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'RES_ACEP' => ['Carta de Aceptación de Residencia Profesional', 'ISC-RES-ACEP', 'carta_aceptacion_residencia.docx'],
            'RES_TERM' => ['Carta de Término de Residencia Profesional', 'ISC-RES-TERM', 'carta_termino_residencia.docx'],
            'SS_ACEP' => ['Carta de Aceptación de Servicio Social', 'ISC-SS-ACEP', 'carta_aceptacion_servicio_social.docx'],
            'SS_TERM' => ['Carta de Término de Servicio Social', 'ISC-SS-TERM', 'carta_termino_servicio_social.docx'],
            'CTA' => ['Constancia de Acreditación de Actividades Complementarias', 'ISC-CTA', 'constancia_actividades_complementarias.docx'],
        ];

        foreach ($tipos as $clave => [$nombre, $prefijo, $archivo]) {
            DB::table('tipos_documento')->updateOrInsert(
                ['clave' => $clave],
                ['nombre' => $nombre, 'prefijo_folio' => $prefijo, 'ruta_plantilla' => $archivo, 'activo' => true]
            );
        }

        // [cargo, etiqueta, orden]
        $carta = [
            ['DESTINATARIO_SS_RESIDENCIAS', 'Destinatario', 1],
            ['JEFE_DIVISION', 'Firma — Jefe de División', 2],
        ];
        $constancia = [
            ['DESTINATARIO_CONSTANCIA', 'Destinatario', 1],
            ['JEFE_DIVISION', 'Firma — Jefe de División', 2],
            ['DOCENTE', 'Vo.Bo. — Docente', 3],
            ['SUBDIRECCION_ACADEMICA', 'Vo.Bo. — Subdirección académica', 4],
        ];

        foreach (['RES_ACEP', 'RES_TERM', 'SS_ACEP', 'SS_TERM'] as $clave) {
            $this->bloques($clave, $carta);
        }
        $this->bloques('CTA', $constancia);
    }

    private function bloques(string $claveTipo, array $bloques): void
    {
        $tipoId = DB::table('tipos_documento')->where('clave', $claveTipo)->value('id');

        foreach ($bloques as [$claveRol, $etiqueta, $orden]) {
            $rolId = DB::table('roles_documento')->where('clave', $claveRol)->value('id');

            DB::table('bloques_firma')->updateOrInsert(
                ['tipo_documento_id' => $tipoId, 'etiqueta' => $etiqueta],
                ['rol_id' => $rolId, 'orden' => $orden, 'obligatorio' => true]
            );
        }
    }
}
