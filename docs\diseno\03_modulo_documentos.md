# Módulo de Documentos — Diseño Definitivo (sin código)

## Decisiones confirmadas

1. **Destinatarios y firmantes = un solo catálogo de personal**, no catálogos separados. Una persona puede tener uno o varios roles.
2. Carta de Aceptación de Residencia **se conserva sin el párrafo institucional**, tal como está en el machote.
3. La "Y" duplicada **se conserva** en el machote hasta que Servicios Escolares confirme el cambio.
4. Servicio Social **sí guarda internamente** institución receptora, programa/actividad, fechas, horas y estado — aunque el machote actual no imprima institución/programa.
5. Se mantiene la separación Estudiante / Trámite / Documento generado / Personal / Folios.
6. Se conserva **archivo .docx + datos estructurados** de cada documento generado.

---

## A–D. Tablas definitivas, campos, tipos y llaves

### `carreras` (catálogo — hoy con 1 registro, para no fijar el dato en código)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| nombre | varchar(150) | — | Sí |
| activo | boolean (default true) | — | Sí |

### `estudiantes`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| nombre_completo | varchar(200) | — | Sí |
| matricula | varchar(20) | UNIQUE | Sí |
| carrera_id | bigint | FK → carreras.id | Sí |
| created_at / updated_at | timestamp | — | — |

*Solo lo permanente del estudiante. Nada de fechas, horas ni proyectos aquí.*

### `personal` (catálogo único de destinatarios/firmantes)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| nombre_completo | varchar(200) (tal como debe imprimirse, ej. "Mtro. Ebner Juárez Elías") | — | Sí |
| puesto_actual | varchar(200) | — | No |
| activo | boolean (default true) | — | Sí |
| created_at / updated_at | timestamp | — | — |

### `roles_documento` (catálogo de cargos/roles que puede ocupar una persona en un documento)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| clave | varchar(50) UNIQUE (ej. `JEFE_DIVISION`, `DOCENTE`, `SUBDIRECCION_ACADEMICA`, `DESTINATARIO_SS_RESIDENCIAS`, `DESTINATARIO_CONSTANCIA`) | — | Sí |
| nombre_visible | varchar(150) | — | Sí |

### `personal_rol` (pivote N:M — una persona puede tener varios roles, sin duplicar el registro de persona)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| personal_id | bigint | FK → personal.id | Sí |
| rol_id | bigint | FK → roles_documento.id | Sí |
| activo | boolean (default true) | — | Sí |
| *(UNIQUE compuesto personal_id + rol_id)* | | | |

### `tipos_documento` (catálogo maestro de los 5 documentos)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| clave | varchar(20) UNIQUE (`RES_ACEP`, `RES_TERM`, `SS_ACEP`, `SS_TERM`, `CTA`) | — | Sí |
| nombre | varchar(150) | — | Sí |
| prefijo_folio | varchar(20) (ej. `ISC-RES-ACEP`) | — | Sí |
| ruta_plantilla | varchar(255) | — | Sí |
| version_plantilla | int (default 1) | — | Sí |
| activo | boolean (default true) | — | Sí |

### `bloques_firma` (define qué bloques de firma/destinatario tiene cada tipo de documento, y en qué orden)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| tipo_documento_id | bigint | FK → tipos_documento.id | Sí |
| rol_id | bigint | FK → roles_documento.id (rol esperado en ese bloque) | Sí |
| etiqueta | varchar(100) (ej. "Destinatario", "Firma — Jefe de División", "Vo.Bo. — Docente") | — | Sí |
| orden | smallint | — | Sí |
| obligatorio | boolean (default true) | — | Sí |

*Esto evita hardcodear "la Constancia tiene 4 bloques y las cartas 2" — queda en base de datos y es lo que impulsa el formulario de selección de firmantes.*

### `tramites_residencia`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| estudiante_id | bigint | FK → estudiantes.id | Sí |
| proyecto | varchar(255) | — | Sí |
| fecha_inicio | date | — | Sí |
| fecha_termino | date | — | Sí |
| horas | smallint | — | Sí |
| estado | enum(`en_curso`,`terminado`,`cancelado`) | — | Sí |
| created_at / updated_at | timestamp | — | — |

### `tramites_servicio_social`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| estudiante_id | bigint | FK → estudiantes.id | Sí |
| institucion_receptora | varchar(255) | — | No *(interno, no se imprime)* |
| programa_actividad | varchar(255) | — | No *(interno)* |
| fecha_inicio | date | — | Sí |
| fecha_termino | date | — | Sí |
| horas | smallint | — | Sí |
| estado | enum(`en_curso`,`terminado`,`cancelado`) | — | Sí |
| created_at / updated_at | timestamp | — | — |

### `tramites_actividad_complementaria`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| estudiante_id | bigint | FK → estudiantes.id | Sí |
| tipo_actividad | varchar(100) (ej. "CURSO MOOC") | — | Sí |
| nombre_actividad | varchar(255) | — | Sí |
| periodo_escolar | varchar(20) (ej. "2025-2026/2") | — | Sí |
| creditos | decimal(3,1) | — | Sí |
| created_at / updated_at | timestamp | — | — |

### `folios_contador`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| tipo_documento_id | bigint | FK → tipos_documento.id (PK compuesta con anio) | Sí |
| anio | smallint (PK compuesta) | — | Sí |
| ultimo_numero | int (default 0) | — | Sí |

*Se actualiza dentro de una transacción con bloqueo de fila (`SELECT ... FOR UPDATE`) al generar cada documento, para que dos generaciones simultáneas nunca obtengan el mismo folio.*

### `documentos_generados`
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| folio | varchar(30) | UNIQUE | Sí |
| tipo_documento_id | bigint | FK → tipos_documento.id | Sí |
| estudiante_id | bigint | FK → estudiantes.id | Sí |
| tramite_type | varchar(100) (relación polimórfica: qué tabla de trámite) | — | Sí |
| tramite_id | bigint (relación polimórfica: id del trámite) | — | Sí |
| version_plantilla_usada | int | — | Sí |
| fecha_generacion | datetime | — | Sí |
| usuario_id | bigint | FK → users.id | Sí |
| nombre_archivo | varchar(255) | — | Sí |
| ruta_archivo | varchar(255) | — | Sí |
| hash_archivo | varchar(64) (sha256) | — | No *(ver nota)* |
| created_at / updated_at | timestamp | — | — |

*`tramite_type` + `tramite_id` es una relación polimórfica: un mismo `documentos_generados` puede apuntar a `tramites_residencia`, `tramites_servicio_social` o `tramites_actividad_complementaria` sin tres columnas FK nullable sueltas.*

### `documento_firmante` (quién ocupó cada bloque de firma en ESE documento específico)
| Campo | Tipo | Llave | Obligatorio |
|---|---|---|---|
| id | bigint | PK | — |
| documento_generado_id | bigint | FK → documentos_generados.id | Sí |
| bloque_firma_id | bigint | FK → bloques_firma.id | Sí |
| personal_id | bigint | FK → personal.id | Sí |
| nombre_snapshot | varchar(200) | — | Sí |
| puesto_snapshot | varchar(200) | — | Sí |

**Por qué "snapshot":** si mañana cambia quién es Jefe de División, los documentos ya generados no deben reescribirse solos — se guarda copia del nombre/puesto tal como aparecieron impresos en ese documento, aunque el catálogo de `personal` cambie después.

---

## E. Relaciones entre tablas

```
carreras 1──N estudiantes
personal N──N roles_documento  (vía personal_rol)
tipos_documento 1──N bloques_firma
tipos_documento 1──N folios_contador (por año)
tipos_documento 1──N documentos_generados
estudiantes 1──N tramites_residencia
estudiantes 1──N tramites_servicio_social
estudiantes 1──N tramites_actividad_complementaria
(tramite_*) 1──N documentos_generados   (vía relación polimórfica)
estudiantes 1──N documentos_generados
documentos_generados 1──N documento_firmante
documento_firmante N──1 personal
documento_firmante N──1 bloques_firma
```

---

## F. Obligatorios vs. opcionales — resumen

**Siempre obligatorio:** folio, tipo de documento, estudiante, trámite relacionado, fecha de generación, usuario que generó, todos los bloques de firma marcados `obligatorio=true`, nombre/ruta del archivo.

**Opcional:** `institucion_receptora` y `programa_actividad` en Servicio Social (son internos, el machote no los imprime), `puesto_actual` en `personal` (informativo), `hash_archivo`.

---

## G–J. Qué pertenece a cada entidad

- **Estudiante**: nombre, matrícula, carrera.
- **Trámite**: fechas, horas, proyecto/institución/programa, periodo escolar, créditos, tipo de actividad, estado del trámite.
- **Documento generado**: folio, tipo, versión de plantilla usada, fecha de generación, usuario, archivo, hash, firmantes de ESE documento (snapshot).
- **Personal/firmantes**: nombre, puesto actual, roles que puede ocupar (catálogo `personal_rol`), estado activo/inactivo.

---

## K. Funcionamiento del folio

1. Al generar un documento, el sistema toma `tipo_documento_id` y el año en curso.
2. Abre una transacción, bloquea la fila de `folios_contador` correspondiente (`SELECT ... FOR UPDATE`), incrementa `ultimo_numero`.
3. Arma el folio: `prefijo_folio` + `-` + `anio` + `-` + número con 5 dígitos (`00001`).
4. Si no existe fila para ese tipo+año, se crea en la misma transacción con `ultimo_numero = 1`.
5. El folio se guarda en `documentos_generados.folio` (UNIQUE) — si por alguna razón hubiera colisión, la restricción UNIQUE de base de datos lo rechaza y se reintenta, nunca se genera un duplicado.

---

## L. Conservación del archivo generado

- Se guarda el `.docx` generado en `storage/app/documentos/{tipo_documento}/{anio}/{folio}.docx` (ruta registrada en `ruta_archivo`).
- Se calcula `hash_archivo` (SHA-256) al momento de generarlo — sirve para verificar después que el archivo descargado no fue alterado, útil si alguna vez se cuestiona la autenticidad de una copia.
- El archivo NO se regenera ni se sobrescribe una vez creado; si se necesita una nueva versión (por corrección de datos), se genera un documento nuevo con folio nuevo, y el anterior puede marcarse como cancelado en vez de borrarse (trazabilidad).

---

## M. Marcadores por machote (para PHPWord `TemplateProcessor`)

### 1. Carta de Aceptación de Residencia Profesional
```
${folio}
${fecha_oficio}
${destinatario_nombre}
${destinatario_puesto}
${nombre_estudiante}
${matricula}
${carrera}
${proyecto}
${fecha_inicio}
${fecha_termino}
${horas}
${firmante_jefe_nombre}
${firmante_jefe_puesto}
```

### 2. Carta de Término de Residencia Profesional
Los mismos 13 marcadores de arriba (el párrafo institucional queda fijo en el Word, no lleva marcador).

### 3. Carta de Aceptación de Servicio Social
```
${folio}
${fecha_oficio}
${destinatario_nombre}
${destinatario_puesto}
${nombre_estudiante}
${matricula}
${carrera}
${fecha_inicio}
${fecha_termino}
${horas}
${firmante_jefe_nombre}
${firmante_jefe_puesto}
```
*(sin `${proyecto}` — no aplica a Servicio Social)*

### 4. Carta de Término de Servicio Social
Los mismos 11 marcadores de la Carta de Aceptación de Servicio Social.

### 5. Constancia de Acreditación de Actividades Complementarias
```
${folio}
${destinatario_nombre}
${destinatario_puesto}
${nombre_estudiante}
${numero_control}
${carrera}
${tipo_actividad}
${nombre_actividad}
${periodo_escolar}
${creditos}
${firmante_jefe_nombre}
${firmante_docente_nombre}
${firmante_subdireccion_nombre}
${firmante_subdireccion_puesto}
```

*Nota: cuando lleguemos a la etapa de preparar los archivos, si algún nombre/puesto ocupa más de una línea en el machote (como el puesto del Jefe de División, que hoy va partido en dos líneas), lo resolvemos ahí — no afecta el diseño de datos, es un detalle de maquetación del marcador dentro del Word.*

---

## N. Flujo completo (selección de estudiante → documento guardado)

1. Usuario autorizado elige el **tipo de documento** (uno de los 5).
2. Busca o registra al **estudiante** (nombre, matrícula, carrera).
3. Según el tipo de documento, captura o selecciona el **trámite** correspondiente (Residencia / Servicio Social / Actividad Complementaria) — si ya existe uno abierto para ese estudiante, se reutiliza en vez de duplicarlo.
4. El sistema muestra los **bloques de firma** definidos para ese tipo de documento (según `bloques_firma`) y el usuario elige, del catálogo `personal`, quién ocupa cada uno (filtrado por el rol que corresponde a cada bloque).
5. El sistema **genera el folio** (transacción atómica sobre `folios_contador`).
6. Se arman los valores de todos los `${marcadores}` con los datos del estudiante, el trámite, el folio y los firmantes seleccionados.
7. **PHPWord `TemplateProcessor`** toma la plantilla (`ruta_plantilla` del tipo de documento) y genera el `.docx` final.
8. Se guarda el archivo en `storage`, se calcula el hash, y se crea el registro en `documentos_generados` + los registros snapshot en `documento_firmante`.
9. El usuario **descarga** el Word para imprimir y recabar firmas físicas.

---

Con este diseño aprobado, el siguiente paso natural son las migraciones de Laravel — pero conforme a tu instrucción, no se programa nada hasta que tú lo confirmes.
