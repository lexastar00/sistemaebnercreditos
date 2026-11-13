<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles y permisos de acceso al sistema (matriz de la Fase 4).
 * Los permisos dicen QUÉ acción puede hacer un rol; el ALCANCE («solo su departamento»)
 * lo aplican las políticas de autorización (Fase 8).
 */
class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'bitacora.ver', 'bitacora.crear', 'bitacora.editar', 'bitacora.cambiar_estado',
            'bitacora.reasignar', 'bitacora.cancelar', 'bitacora.adjuntar_evidencia',
            'documentos.generar', 'documentos.ver_historial', 'documentos.descargar',
            'estudiantes.ver', 'estudiantes.crear', 'estudiantes.editar',
            'seguimiento.ver', 'seguimiento.ver_contacto',
            'catalogos.administrar', 'usuarios.administrar', 'auditoria.ver', 'configuracion.administrar',
            'dashboard.ver_global', 'dashboard.ver_departamento',
        ];

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        $roles = [
            'Administrador' => $permisos,
            'Jefe de Área' => [
                'bitacora.ver', 'bitacora.crear', 'bitacora.editar', 'bitacora.cambiar_estado',
                'bitacora.reasignar', 'bitacora.cancelar', 'bitacora.adjuntar_evidencia',
                'documentos.generar', 'documentos.ver_historial', 'documentos.descargar',
                'estudiantes.ver', 'estudiantes.crear', 'estudiantes.editar',
                'seguimiento.ver', 'seguimiento.ver_contacto', 'dashboard.ver_departamento',
            ],
            'Secretario' => [
                'bitacora.ver', 'bitacora.crear', 'bitacora.editar', 'bitacora.adjuntar_evidencia',
                'documentos.generar', 'documentos.ver_historial', 'documentos.descargar',
                'estudiantes.ver', 'estudiantes.crear', 'estudiantes.editar',
            ],
            'Personal de Sistemas' => [
                'bitacora.ver', 'bitacora.editar', 'bitacora.cambiar_estado', 'bitacora.adjuntar_evidencia',
            ],
            // Solo lectura: ve la ubicación de los estudiantes pero NO su teléfono ni correo.
            'Usuario de Consulta' => [
                'bitacora.ver', 'documentos.ver_historial', 'estudiantes.ver', 'seguimiento.ver', 'dashboard.ver_global',
            ],
        ];

        foreach ($roles as $nombre => $permisosDelRol) {
            Role::findOrCreate($nombre, 'web')->syncPermissions($permisosDelRol);
        }
    }
}
