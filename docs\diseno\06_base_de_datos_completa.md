# Base de datos completa e integrada — Fase 5

Integra Documentos (Fase 2), Bitácora (Fase 3), Usuarios/Roles (Fase 4) y el nuevo módulo de **seguimiento de estudiantes**. Sin tablas duplicadas: un solo catálogo `personal`, un solo `estudiantes`, y los trámites cuelgan del estudiante.

## 1. Mapa de tablas (30)

| Grupo | Tablas |
|---|---|
| Seguridad | `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`, `activity_log` *(las 6 últimas las crean Spatie)* |
| Catálogos | `carreras`, `departamentos`, `servicios`, `tipos_atencion`, `tipos_problema`, `estados_atencion`, `periodos_escolares`, `roles_documento` |
| Personal | `personal`, `personal_rol` |
| Estudiantes y trámites | `estudiantes`, `tramites_residencia`, `tramites_servicio_social`, `tramites_actividad_complementaria` |
| Bitácora | `atenciones`, `folios_atencion`, `atencion_adjuntos`, `atencion_seguimientos` |
| Documentos | `tipos_documento`, `bloques_firma`, `folios_contador`, `documentos_generados`, `documento_firmante` |

## 2. Cambios respecto a los diseños anteriores

1. **`personal`** gana `departamento_id` (FK nullable) — necesario para el alcance "solo su departamento" de Jefe, Secretario y Personal de Sistemas.
2. **`users.personal_id`** (FK nullable, único) — un usuario que también firma o atiende es el mismo registro de `personal`.
3. **`estudiantes`** gana: `semestre`, `correo_institucional`, `telefono`, `periodo_id`, `estatus`. Es la base de la tarjeta de datos.
4. **`tramites_residencia`** gana: `empresa_institucion`, `asesor_interno_id` (FK → personal), `asesor_externo`, `periodo_id`. *(Estaban en tu spec original y se me habían quedado fuera.)*
5. **`tramites_servicio_social`** gana `periodo_id`.
6. **`servicios`** pertenece a un departamento (`departamento_id`). Las atenciones **no** guardan departamento: se obtiene por `servicio → departamento`, así no pueden contradecirse.
8. **Entradas y salidas del Área de Sistemas**: toda entrada es una atención, así que no hay tabla nueva; `atenciones` gana `tipo_solicitante` y `hora_salida`. El conteo diario de alumnos y docentes que entran y se atienden sale de esa misma tabla.
7. **Seguimiento de estudiantes no crea tabla nueva**: es una consulta sobre `estudiantes` + trámites en curso (ver sección 5).

## 3. Tablas nuevas o modificadas (campos completos)

### `estudiantes`
| Campo | Tipo | Obligatorio |
|---|---|---|
| id | bigint PK | — |
| nombre_completo | varchar(200) | Sí |
| matricula | varchar(20) UNIQUE | Sí |
| carrera_id | FK → carreras | Sí |
| semestre | tinyint | No |
| correo_institucional | varchar(150) | No |
| telefono | varchar(20) | No |
| periodo_id | FK → periodos_escolares | No |
| estatus | enum(`activo`,`egresado`,`baja`) | Sí (default `activo`) |
| created_at / updated_at | timestamp | — |

### `personal`
`id` · `nombre_completo` (Sí) · `puesto_actual` (No) · `departamento_id` FK → departamentos (No) · `activo` (Sí) · timestamps

### `servicios`
`id` · `nombre` (Sí) · `departamento_id` FK → departamentos (Sí) · `activo` (Sí)

### `tramites_residencia`
`id` · `estudiante_id` FK · `proyecto` (Sí) · `empresa_institucion` (Sí) · `asesor_interno_id` FK → personal (No) · `asesor_externo` varchar (No) · `periodo_id` FK (No) · `fecha_inicio` · `fecha_termino` · `horas` · `estado` enum(`en_curso`,`terminado`,`cancelado`) · timestamps

### `tramites_servicio_social`
`id` · `estudiante_id` FK · `institucion_receptora` (Sí; **se vuelve obligatoria** porque alimenta el seguimiento) · `programa_actividad` (No) · `periodo_id` FK (No) · `fecha_inicio` · `fecha_termino` · `horas` · `estado` enum(`en_curso`,`terminado`,`cancelado`) · timestamps

### `tramites_actividad_complementaria`
Sin cambios: `id` · `estudiante_id` FK · `tipo_actividad` · `nombre_actividad` · `periodo_escolar` varchar · `creditos` decimal(3,1) · timestamps

### `atenciones` (versión final)
| Campo | Tipo | Obligatorio |
|---|---|---|
| id | bigint PK | — |
| folio | varchar(20) UNIQUE | Sí |
| fecha_hora | datetime | Sí *(fecha y hora de entrada)* |
| tipo_solicitante | enum(alumno, docente, administrativo, otro) | Sí (por omisión `alumno`) |
| hora_salida | datetime | No *(vacía mientras la persona sigue en el área)* |
| solicitante_nombre | varchar(200) | Sí |
| matricula | varchar(20) | No |
| estudiante_id | FK → estudiantes | No *(se llena si el solicitante es un alumno registrado)* |
| asunto | varchar(255) | Sí |
| jefe_id | FK → personal | Sí |
| secretario_id | FK → personal | Sí |
| atendido_por_id | FK → personal | No |
| servicio_id | FK → servicios | Sí |
| tipo_atencion_id | FK → tipos_atencion | Sí |
| tipo_problema_id | FK → tipos_problema | No |
| descripcion_problema | text | Sí |
| solucion | text | No |
| observaciones | text | No |
| estado_id | FK → estados_atencion | Sí |
| usuario_registro_id | FK → users | Sí |
| created_at / updated_at | timestamp | — |

**Índices:** `folio` (único), `fecha_hora`, `estado_id`, `servicio_id`, `atendido_por_id`, `matricula`.

### `folios_atencion`
`anio` smallint PK · `ultimo_numero` int. Mismo bloqueo atómico que `folios_contador`.

### `atencion_adjuntos`, `atencion_seguimientos`
Como en la Fase 3 (seguimientos con `responsable_anterior_id` y `responsable_nuevo_id`).

### Sin cambios
`carreras`, `departamentos`, `tipos_atencion`, `tipos_problema`, `estados_atencion`, `periodos_escolares`, `roles_documento`, `personal_rol`, `tipos_documento`, `bloques_firma`, `folios_contador`, `documentos_generados`, `documento_firmante`: tal como quedaron aprobadas.

## 4. Relaciones principales

```
carreras 1─N estudiantes
estudiantes 1─N tramites_residencia / tramites_servicio_social / tramites_actividad_complementaria
estudiantes 1─N documentos_generados      (trámite ─ documento vía relación polimórfica)
departamentos 1─N servicios · departamentos 1─N personal
personal N─N roles_documento (personal_rol)
personal 1─N atenciones (jefe, secretario, atendió) · personal 1─N tramites_residencia (asesor interno)
servicios / tipos_atencion / tipos_problema / estados_atencion 1─N atenciones
atenciones 1─N atencion_adjuntos · atenciones 1─N atencion_seguimientos
users 0..1─1 personal · users N─N roles (Spatie) · users 1─N atenciones / documentos_generados
```

## 5. Módulo de seguimiento de estudiantes (sin tabla nueva)

**Qué ve el jefe de carrera, por estudiante:** estatus en el sistema y, según su trámite en curso:
- **Servicio Social** → institución receptora, programa, fechas, horas.
- **Residencia Profesional** → empresa/institución, proyecto, asesor interno y externo, fechas.
- **Sin trámite en curso** → se muestra como tal.

**Tarjeta de datos:** nombre, matrícula, carrera, semestre, correo institucional, teléfono.

**Reglas:**
- La ubicación sale del trámite con `estado = en_curso`, no se captura aparte (no se duplica).
- Un estudiante no debe tener dos trámites del mismo tipo en curso a la vez (validación en la aplicación).
- **Permisos nuevos:** `seguimiento.ver` y `seguimiento.ver_contacto`. Propuesta: Administrador y Jefe de Área ven todo; Usuario de Consulta ve la ubicación pero **no** el teléfono ni el correo (minimiza datos personales); Secretario y Personal de Sistemas no acceden.
- Al jefe de carrera le corresponde el rol **Jefe de Área** ya definido; no se crea un rol nuevo.

## 6. Datos iniciales (seeders)

- `carreras`: Ingeniería en Sistemas Computacionales (único registro).
- `estados_atencion`: Registrada, En proceso, Pendiente, Resuelta, Cancelada, Reabierta.
- `roles_documento`: JEFE_DIVISION, DOCENTE, SUBDIRECCION_ACADEMICA, DESTINATARIO_SS_RESIDENCIAS, DESTINATARIO_CONSTANCIA, SECRETARIO, PERSONAL_SISTEMAS, JEFE_AREA.
- `tipos_documento` (5) y sus `bloques_firma`.
- Roles y permisos de Spatie según la matriz de la Fase 4 más los dos permisos nuevos.
- `departamentos`, `servicios`, `tipos_atencion`, `tipos_problema`: **valores de ejemplo editables** hasta que pases la lista real.

## 7. Pendientes que no bloquean las migraciones

1. Lista real de departamentos, servicios, tipos de atención y tipos de problema.
2. Confirmar la propuesta de privacidad de la tarjeta (Consulta no ve teléfono ni correo).
3. Hosting (solo afecta el despliegue final).

**Con esto la Fase 5 queda lista para tu aprobación. Al aprobarla arrancan las migraciones (Fase 6).**
