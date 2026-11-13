# Diccionario de datos

Describe cada tabla propia del sistema, para qué sirve y qué guarda cada campo. Se genera a partir del mismo diseño que las migraciones de Laravel, de modo que documento y base de datos no se contradicen.

Además de estas tablas, el sistema usa las tablas estándar de Laravel (`sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`) y las 5 de Spatie Permission (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`), que crean el framework y el paquete.

## Catálogos

### `carreras`

Catálogo de carreras. Hoy solo contiene Ingeniería en Sistemas Computacionales; existe como tabla para no escribir el nombre de la carrera en el código.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(150) | Sí | — | Nombre completo de la carrera. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `departamentos`

Catálogo de departamentos o áreas de la División. Se usa para limitar lo que ve cada rol (alcance «su departamento»).

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(150) | Sí | — | Nombre del departamento. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `servicios`

Catálogo de servicios que presta el Área de Sistemas (ej. soporte técnico). Cada servicio pertenece a un departamento.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(150) | Sí | — | Nombre del servicio. |
| `departamento_id` | bigint sin signo (llave foránea) | Sí | FK → departamentos | Departamento al que pertenece el servicio. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `tipos_atencion`

Catálogo de tipos de atención (ej. soporte, instalación, asesoría).

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(150) | Sí | — | Nombre del tipo de atención. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `tipos_problema`

Catálogo de tipos de problema (ej. hardware, software, red).

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(150) | Sí | — | Nombre del tipo de problema. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `estados_atencion`

Catálogo de estados del ciclo de vida de una atención (Registrada, En proceso, Pendiente, Resuelta, Cancelada, Reabierta). Es tabla para poder ajustarlos sin cambiar código.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(50) | Sí | Único | Nombre del estado. |
| `orden` | smallint | Sí | — | Posición del estado al mostrarlo en listas. Valor por omisión: 0. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `periodos_escolares`

Catálogo de periodos escolares con sus fechas. Solo uno debería estar activo a la vez.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre` | varchar(30) | Sí | Único | Nombre del periodo (ej. 2025-2026/2). |
| `fecha_inicio` | date | Sí | — | Primer día del periodo. |
| `fecha_fin` | date | Sí | — | Último día del periodo. |
| `activo` | boolean | Sí | — | Indica si es el periodo vigente. Valor por omisión: no. |

### `roles_documento`

Catálogo de cargos que una persona puede ocupar al firmar o recibir un documento (Jefe de División, Docente, Destinatario…). No son los roles de acceso al sistema.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `clave` | varchar(50) | Sí | Único | Clave interna única del cargo (ej. JEFE_DIVISION). |
| `nombre_visible` | varchar(150) | Sí | — | Nombre que ve el usuario. |

## Personal

### `personal`

Catálogo único de personas que firman, reciben documentos o son responsables en la bitácora. Una persona aparece una sola vez aunque tenga varios cargos.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre_completo` | varchar(200) | Sí | — | Nombre tal como debe imprimirse (incluye grado, ej. «Mtro. Ebner Juárez Elías»). |
| `puesto_actual` | varchar(200) | No | — | Puesto actual de la persona. |
| `departamento_id` | bigint sin signo (llave foránea) | No | FK → departamentos | Departamento al que pertenece (para limitar el alcance de los roles). |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

### `personal_rol`

Relación entre personal y cargos: permite que una persona tenga uno o varios cargos sin duplicarla.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `personal_id` | bigint sin signo (llave foránea) | Sí | FK → personal | Persona. Si se elimina el registro padre, se eliminan estos. |
| `rol_id` | bigint sin signo (llave foránea) | Sí | FK → roles_documento | Cargo que puede ocupar. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

**Restricciones e índices:** Combinación única: `personal_id` + `rol_id`.

## Seguridad

### `activity_log`

Registro de auditoría: quién hizo qué, sobre qué registro y cuándo. Su estructura es compatible con Spatie Activitylog; en la Fase 13 se decide si se usa el paquete o un registro propio.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `log_name` | varchar(255) | No | — | Categoría del evento. |
| `description` | text | Sí | — | Descripción de la acción realizada. |
| `subject_type` | varchar(255) | No | — | Tipo de registro afectado (nombre del modelo). |
| `event` | varchar(255) | No | — | Tipo de evento (creado, modificado, eliminado…). |
| `subject_id` | bigint sin signo | No | — | Identificador del registro afectado. |
| `causer_type` | varchar(255) | No | — | Tipo de quien realizó la acción. |
| `causer_id` | bigint sin signo | No | — | Identificador de quien realizó la acción (usuario). |
| `properties` | json | No | — | Detalle del cambio (valores antes y después). |
| `batch_uuid` | char(36) | No | — | Agrupa acciones realizadas juntas. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `log_name`; Índice de búsqueda: `subject_type` + `subject_id`; Índice de búsqueda: `causer_type` + `causer_id`.

## Estudiantes y trámites

### `estudiantes`

Registro central de estudiantes. Guarda solo datos permanentes; fechas, horas y proyectos viven en los trámites.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `nombre_completo` | varchar(200) | Sí | — | Nombre completo del estudiante. |
| `matricula` | varchar(20) | Sí | Único | Matrícula o número de control (única). |
| `carrera_id` | bigint sin signo (llave foránea) | Sí | FK → carreras | Carrera que cursa. |
| `semestre` | tinyint sin signo | No | — | Semestre actual. |
| `correo_institucional` | varchar(150) | No | — | Correo institucional. |
| `telefono` | varchar(20) | No | — | Teléfono de contacto (dato personal: acceso restringido). |
| `periodo_id` | bigint sin signo (llave foránea) | No | FK → periodos_escolares | Periodo escolar vigente del estudiante. |
| `estatus` | enum(activo, egresado, baja) | Sí | — | Situación del estudiante en la institución. Valor por omisión: activo. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `nombre_completo`.

### `tramites_residencia`

Trámite de Residencia Profesional de un estudiante. Alimenta la Carta de Aceptación, la de Término y el seguimiento.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `estudiante_id` | bigint sin signo (llave foránea) | Sí | FK → estudiantes | Estudiante que realiza la residencia. |
| `proyecto` | varchar(255) | Sí | — | Nombre del proyecto de residencia. |
| `empresa_institucion` | varchar(255) | Sí | — | Empresa o institución donde se realiza. |
| `asesor_interno_id` | bigint sin signo (llave foránea) | No | FK → personal | Asesor interno (docente). |
| `asesor_externo` | varchar(200) | No | — | Asesor externo, si aplica. |
| `periodo_id` | bigint sin signo (llave foránea) | No | FK → periodos_escolares | Periodo escolar. |
| `fecha_inicio` | date | Sí | — | Fecha de inicio. |
| `fecha_termino` | date | Sí | — | Fecha de término. |
| `horas` | smallint sin signo | Sí | — | Total de horas. |
| `estado` | enum(en_curso, terminado, cancelado) | Sí | — | Estado del trámite. Valor por omisión: en_curso. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `estudiante_id` + `estado`.

### `tramites_servicio_social`

Trámite de Servicio Social de un estudiante. Alimenta sus cartas y el seguimiento.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `estudiante_id` | bigint sin signo (llave foránea) | Sí | FK → estudiantes | Estudiante que realiza el servicio. |
| `institucion_receptora` | varchar(255) | Sí | — | Institución donde presta el servicio (se guarda aunque la carta no la imprima). |
| `programa_actividad` | varchar(255) | No | — | Programa o actividad. |
| `periodo_id` | bigint sin signo (llave foránea) | No | FK → periodos_escolares | Periodo escolar. |
| `fecha_inicio` | date | Sí | — | Fecha de inicio. |
| `fecha_termino` | date | Sí | — | Fecha de término. |
| `horas` | smallint sin signo | Sí | — | Total de horas. |
| `estado` | enum(en_curso, terminado, cancelado) | Sí | — | Estado del trámite. Valor por omisión: en_curso. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `estudiante_id` + `estado`.

### `tramites_actividad_complementaria`

Actividad complementaria acreditada a un estudiante. Alimenta la Constancia de Acreditación.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `estudiante_id` | bigint sin signo (llave foránea) | Sí | FK → estudiantes | Estudiante que acredita la actividad. |
| `tipo_actividad` | varchar(100) | Sí | — | Tipo de actividad (ej. CURSO MOOC). |
| `nombre_actividad` | varchar(255) | Sí | — | Nombre de la actividad o curso. |
| `periodo_escolar` | varchar(20) | Sí | — | Periodo escolar tal como se imprime (ej. 2025-2026/2). |
| `creditos` | decimal(3,1) | Sí | — | Créditos otorgados. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

## Documentos

### `tipos_documento`

Catálogo de los 5 documentos oficiales: su prefijo de folio, su plantilla de Word y la versión de esa plantilla.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `clave` | varchar(20) | Sí | Único | Clave interna única (RES_ACEP, RES_TERM, SS_ACEP, SS_TERM, CTA). |
| `nombre` | varchar(150) | Sí | — | Nombre oficial del documento. |
| `prefijo_folio` | varchar(20) | Sí | — | Prefijo del folio (ej. ISC-RES-ACEP). |
| `ruta_plantilla` | varchar(255) | Sí | — | Ubicación del archivo .docx usado como plantilla. |
| `version_plantilla` | int sin signo | Sí | — | Versión vigente de la plantilla. Valor por omisión: 1. |
| `activo` | boolean | Sí | — | Indica si el registro está en uso (1) o dado de baja (0). Valor por omisión: sí. |

### `bloques_firma`

Define qué bloques de firma o destinatario lleva cada tipo de documento y en qué orden.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `tipo_documento_id` | bigint sin signo (llave foránea) | Sí | FK → tipos_documento | Documento al que pertenece el bloque. |
| `rol_id` | bigint sin signo (llave foránea) | Sí | FK → roles_documento | Cargo esperado en ese bloque. |
| `etiqueta` | varchar(100) | Sí | — | Nombre del bloque (ej. «Vo.Bo. — Docente»). |
| `orden` | smallint | Sí | — | Orden de aparición. |
| `obligatorio` | boolean | Sí | — | Indica si el bloque debe llenarse para generar el documento. Valor por omisión: sí. |

### `folios_contador`

Contador de folios por tipo de documento y año. Se incrementa con bloqueo para que nunca se repita un folio.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `tipo_documento_id` | bigint sin signo (llave foránea) | Sí | PK, FK → tipos_documento | Tipo de documento. |
| `anio` | smallint sin signo | Sí | PK | Año del consecutivo. |
| `ultimo_numero` | int sin signo | Sí | — | Último consecutivo asignado. Valor por omisión: 0. |

**Restricciones e índices:** Llave primaria compuesta: `tipo_documento_id` + `anio`.

### `documentos_generados`

Registro de cada documento Word generado: folio, estudiante, trámite, plantilla usada, archivo y quién lo generó.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `folio` | varchar(30) | Sí | Único | Folio único del documento (ej. ISC-CTA-2026-00001). |
| `tipo_documento_id` | bigint sin signo (llave foránea) | Sí | FK → tipos_documento | Tipo de documento. |
| `estudiante_id` | bigint sin signo (llave foránea) | Sí | FK → estudiantes | Estudiante relacionado. |
| `tramite_type` | varchar(100) | Sí | — | Tabla del trámite relacionado (relación polimórfica). |
| `tramite_id` | bigint sin signo | Sí | — | Identificador del trámite relacionado. |
| `version_plantilla_usada` | int sin signo | Sí | — | Versión de la plantilla con la que se generó. |
| `fecha_generacion` | datetime | Sí | — | Fecha y hora de generación. |
| `usuario_id` | bigint sin signo (llave foránea) | Sí | FK → users | Usuario que generó el documento. |
| `nombre_archivo` | varchar(255) | Sí | — | Nombre del archivo generado. |
| `ruta_archivo` | varchar(255) | Sí | — | Ubicación del archivo guardado. |
| `hash_archivo` | char(64) | No | — | Huella SHA-256 del archivo, para comprobar que no fue alterado. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `tramite_type` + `tramite_id`; Índice de búsqueda: `fecha_generacion`.

### `documento_firmante`

Quién ocupó cada bloque de firma en un documento concreto. Guarda copia del nombre y puesto para que un cambio de personal no altere documentos ya emitidos.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `documento_generado_id` | bigint sin signo (llave foránea) | Sí | FK → documentos_generados | Documento generado. Si se elimina el registro padre, se eliminan estos. |
| `bloque_firma_id` | bigint sin signo (llave foránea) | Sí | FK → bloques_firma | Bloque de firma ocupado. |
| `personal_id` | bigint sin signo (llave foránea) | Sí | FK → personal | Persona seleccionada. |
| `nombre_snapshot` | varchar(200) | Sí | — | Nombre impreso en el documento. |
| `puesto_snapshot` | varchar(200) | Sí | — | Puesto impreso en el documento. |

## Bitácora

### `folios_atencion`

Contador de folios de la bitácora (SIS-AAAA-000001) por año.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `anio` | smallint sin signo | Sí | PK | Año del consecutivo. |
| `ultimo_numero` | int sin signo | Sí | — | Último consecutivo asignado. Valor por omisión: 0. |

### `atenciones`

Registro principal de la bitácora: toda entrada de un alumno, docente u otra persona al Área de Sistemas es una atención, con su hora de entrada y salida, problema, solución, responsable y estado actual.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `folio` | varchar(20) | Sí | Único | Folio único (ej. SIS-2026-000001). |
| `fecha_hora` | datetime | Sí | — | Fecha y hora de entrada: inicio de la atención. |
| `tipo_solicitante` | enum(alumno, docente, administrativo, otro) | Sí | — | Tipo de persona que entra y solicita la atención; permite contar alumnos y docentes por día. Valor por omisión: alumno. |
| `hora_salida` | datetime | No | — | Fecha y hora de salida. Queda vacía mientras la persona sigue en el área. |
| `solicitante_nombre` | varchar(200) | Sí | — | Nombre de quien solicita la atención. |
| `matricula` | varchar(20) | No | — | Matrícula del solicitante (solo si es estudiante). |
| `estudiante_id` | bigint sin signo (llave foránea) | No | FK → estudiantes | Estudiante registrado, si el solicitante lo es. |
| `asunto` | varchar(255) | Sí | — | Motivo de la visita o solicitud. |
| `jefe_id` | bigint sin signo (llave foránea) | Sí | FK → personal | Jefe responsable. |
| `secretario_id` | bigint sin signo (llave foránea) | Sí | FK → personal | Secretario responsable. |
| `atendido_por_id` | bigint sin signo (llave foránea) | No | FK → personal | Persona que atendió (se asigna al iniciar la atención). |
| `servicio_id` | bigint sin signo (llave foránea) | Sí | FK → servicios | Servicio que atendió (de aquí se obtiene el departamento). |
| `tipo_atencion_id` | bigint sin signo (llave foránea) | Sí | FK → tipos_atencion | Tipo de atención. |
| `tipo_problema_id` | bigint sin signo (llave foránea) | No | FK → tipos_problema | Tipo de problema. |
| `descripcion_problema` | text | Sí | — | Descripción del problema. |
| `solucion` | text | No | — | Solución proporcionada. |
| `observaciones` | text | No | — | Observaciones adicionales. |
| `estado_id` | bigint sin signo (llave foránea) | Sí | FK → estados_atencion | Estado actual de la atención. |
| `usuario_registro_id` | bigint sin signo (llave foránea) | Sí | FK → users | Usuario que registró la atención. |
| `created_at`, `updated_at` | timestamp | No | — | Fecha de creación y de última modificación del registro. |

**Restricciones e índices:** Índice de búsqueda: `fecha_hora`; Índice de búsqueda: `matricula`.

### `atencion_adjuntos`

Evidencias (imágenes o PDF) adjuntas a una atención.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `atencion_id` | bigint sin signo (llave foránea) | Sí | FK → atenciones | Atención a la que pertenece. Si se elimina el registro padre, se eliminan estos. |
| `nombre_archivo` | varchar(255) | Sí | — | Nombre original del archivo. |
| `ruta_archivo` | varchar(255) | Sí | — | Ubicación del archivo guardado. |
| `tipo_mime` | varchar(100) | Sí | — | Tipo de archivo. |
| `tamano_kb` | int sin signo | Sí | — | Tamaño en kilobytes. |
| `usuario_id` | bigint sin signo (llave foránea) | Sí | FK → users | Usuario que lo subió. |
| `fecha_subida` | timestamp (fecha actual por omisión) | Sí | — | Fecha y hora en que se subió. |

### `atencion_seguimientos`

Historial de una atención: cada cambio de estado, de responsable o comentario queda como una fila.

| Campo | Tipo | Obligatorio | Llave | Descripción |
|---|---|---|---|---|
| `id` | bigint sin signo (autonumérico) | Sí | PK | Identificador único del registro. |
| `atencion_id` | bigint sin signo (llave foránea) | Sí | FK → atenciones | Atención. Si se elimina el registro padre, se eliminan estos. |
| `usuario_id` | bigint sin signo (llave foránea) | Sí | FK → users | Usuario que realizó el cambio. |
| `estado_anterior_id` | bigint sin signo (llave foránea) | No | FK → estados_atencion | Estado antes del cambio. |
| `estado_nuevo_id` | bigint sin signo (llave foránea) | Sí | FK → estados_atencion | Estado después del cambio. |
| `responsable_anterior_id` | bigint sin signo (llave foránea) | No | FK → personal | Responsable antes del cambio. |
| `responsable_nuevo_id` | bigint sin signo (llave foránea) | No | FK → personal | Responsable después del cambio. |
| `comentario` | text | No | — | Comentario del seguimiento. |
| `fecha` | timestamp (fecha actual por omisión) | Sí | — | Fecha y hora del cambio. |

**Restricciones e índices:** Índice de búsqueda: `atencion_id` + `fecha`.
