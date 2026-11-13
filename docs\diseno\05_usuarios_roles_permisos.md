# Usuarios, Roles y Permisos — Diseño (Fase 4)

## 1. Tabla `users`

| Campo | Tipo | Obligatorio |
|---|---|---|
| id | bigint PK | — |
| name | varchar(150) | Sí |
| email | varchar(150) UNIQUE | Sí |
| password | varchar(255) (hash) | Sí |
| personal_id | bigint FK → personal.id, nullable | No |
| activo | boolean (default true) | Sí |
| email_verified_at | timestamp, nullable | No |
| remember_token | varchar(100), nullable | No |
| created_at / updated_at | timestamp | — |

**`personal_id` es la pieza clave de esta fase:** si un usuario del sistema (ej. el Jefe de División) también aparece como firmante en Documentos o como responsable en Bitácora, es **el mismo registro** de `personal`, no uno duplicado. Un usuario de consulta que nunca firma nada puede tener `personal_id` nulo.

*Ajuste al catálogo `personal` ya definido: le agrego `departamento_id` (FK → departamentos, nullable) — lo necesito para poder limitar lo que ve un Jefe a su propio departamento, cosa que tu spec original ya pedía ("Jefe: puede consultar y administrar las funciones correspondientes a su área") pero que no habíamos modelado todavía.*

## 2. Roles (tabla `roles` de Spatie)

Confirmo los 5 que propusiste, sin agregar ni quitar — pero si durante la Fase 9 (construir Bitácora) aparece la necesidad de un rol más específico, se agrega sin romper nada porque el permiso, no el rol, es lo que controla el acceso real:

- Administrador
- Jefe de Área
- Secretario
- Personal de Sistemas
- Usuario de Consulta

## 3. Permisos (tabla `permissions` de Spatie) — agrupados por módulo

| Módulo | Permisos |
|---|---|
| Bitácora | `bitacora.ver`, `bitacora.crear`, `bitacora.editar`, `bitacora.cambiar_estado`, `bitacora.reasignar`, `bitacora.cancelar`, `bitacora.adjuntar_evidencia` |
| Documentos | `documentos.generar`, `documentos.ver_historial`, `documentos.descargar` |
| Estudiantes | `estudiantes.ver`, `estudiantes.crear`, `estudiantes.editar` |
| Catálogos | `catalogos.administrar` (personal, servicios, departamentos, tipos, estados, periodos) |
| Usuarios | `usuarios.administrar` (crear, editar, asignar roles) |
| Auditoría | `auditoria.ver` |
| Configuración | `configuracion.administrar` |
| Dashboard | `dashboard.ver_global`, `dashboard.ver_departamento` |

Tablas estándar de `spatie/laravel-permission` que esto usa (no se reinventan): `roles`, `permissions`, `role_has_permissions` (qué permisos trae cada rol por default), `model_has_roles` y `model_has_permissions` (qué rol/permiso tiene cada usuario — permite además dar un permiso suelto a un usuario específico sin cambiarle el rol completo, útil para excepciones puntuales).

## 4. Matriz de permisos por rol

| Acción | Administrador | Jefe de Área | Secretario | Personal de Sistemas | Usuario de Consulta |
|---|---|---|---|---|---|
| Ver atenciones | Todas | Solo su departamento | Solo su departamento | Todas las de su departamento | Todas (solo lectura) |
| Crear atención | ✅ | ✅ | ✅ | ❌ | ❌ |
| Editar atención (solución, observaciones) | ✅ | ✅ (su depto) | ✅ (su depto) | ✅ (su depto) | ❌ |
| Cambiar estado | ✅ | ✅ (su depto) | ❌ | ✅ (su depto) | ❌ |
| Reasignar responsable | ✅ | ✅ (su depto) | ❌ | ❌ | ❌ |
| Cancelar atención | ✅ | ✅ (su depto) | ❌ | ❌ | ❌ |
| Generar documentos | ✅ | ✅ | ✅ | ❌ | ❌ |
| Ver historial de documentos | ✅ | ✅ | ✅ | ❌ | Todos (solo lectura) |
| Administrar catálogos | ✅ | ❌ | ❌ | ❌ | ❌ |
| Administrar usuarios/roles | ✅ | ❌ | ❌ | ❌ | ❌ |
| Ver auditoría | ✅ | ❌ | ❌ | ❌ | ❌ |
| Dashboard | Global | Su departamento | ❌ | ❌ | Global (solo lectura) |

**Nota técnica importante:** los permisos de Spatie responden "¿puede hacer esta acción?" (sí/no). El **alcance** ("su departamento", "solo las suyas") no lo resuelve Spatie por sí solo — se implementa con **Policies de Laravel** que, además de checar el permiso, comparan `atenciones.servicio_id`/`departamento` contra `auth()->user()->personal->departamento_id`. Esto no cambia el diseño de tablas, es una capa de código que se construye en la Fase 8.

## 5. Decisiones cerradas de la Fase 4

1. **Usuario de Consulta**: alcance global (todo el sistema), solo lectura — pensado para Dirección/Contraloría, no para un jefe de carrera con alcance limitado.
2. **Personal de Sistemas**: ve y puede editar/cambiar estado de todas las atenciones de su departamento, no solo las asignadas a él (se ajustó la matriz arriba).
3. **Multi-rol confirmado**: un usuario puede tener más de un rol a la vez (ej. Secretario + Personal de Sistemas). No se necesita tabla extra — `model_has_roles` de Spatie ya soporta N roles por usuario de forma nativa.

**Fase 4 — Usuarios/Roles/Permisos: cerrada.** Sigue la Fase 5 (BD completa integrada).
