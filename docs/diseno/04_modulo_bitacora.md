# Módulo de Bitácora de Sistemas — Diseño (Fase 3)

## 1. Campos que captura el personal (de tu spec original)

| Campo | Tipo | Obligatorio | Origen |
|---|---|---|---|
| Folio | varchar(20) | Sí | Sistema (autogenerado) |
| Fecha y hora | datetime | Sí | Sistema (al registrar) |
| Nombre del solicitante | varchar(200) | Sí | Captura |
| Matrícula | varchar(20) | No *(puede ser personal, no solo estudiantes)* | Captura o Estudiante |
| Asunto / motivo | varchar(255) | Sí | Captura |
| Jefe responsable | relación | Sí | Catálogo `personal` |
| Secretario responsable | relación | Sí | Catálogo `personal` |
| Persona que atendió | relación | No *(se asigna al iniciar atención, no siempre desde el registro)* | Catálogo `personal` |
| Servicio/departamento | relación | Sí | Catálogo `servicios` / `departamentos` |
| Tipo de atención | relación | Sí | Catálogo `tipos_atencion` |
| Tipo de problema | relación | No *(depende del tipo de atención)* | Catálogo `tipos_problema` |
| Descripción del problema | text | Sí | Captura |
| Solución proporcionada | text | No *(se llena al resolver)* | Captura |
| Observaciones | text | No | Captura |
| Estado | relación | Sí | Catálogo `estados_atencion` |
| Usuario que registró | relación | Sí | `users` (quien tiene la sesión) |
| Fecha/hora de creación | timestamp | Sí | Sistema |
| Fecha/hora de última modificación | timestamp | Sí | Sistema |

*Nota: "Matrícula" la dejo opcional porque quien solicita atención no siempre es estudiante (puede ser personal administrativo o docente) — si confirmas que solo estudiantes solicitan, la hacemos obligatoria.*

## 2. Folio

Formato: `SIS-2026-000001` (tal como propusiste), consecutivo único, sin reiniciarse por tipo (a diferencia de Documentos, aquí solo hay un tipo de trámite: la atención). Se controla igual que en Documentos: transacción con bloqueo de fila sobre un contador por año, para evitar duplicados con captura simultánea.

## 3. Estados — análisis de tu propuesta

Propusiste: Registrada, En proceso, Pendiente, Resuelta, Cancelada. Es una base sólida, pero noto dos huecos:

- **Falta "Reabierta"**: si una atención marcada como Resuelta regresa porque el problema persiste, hoy no hay estado para eso sin perder el historial de que ya se había dado por resuelta una vez. Sin este estado, se tendría que crear una atención nueva y se pierde la trazabilidad del caso original.
- **"Pendiente" es ambiguo frente a "En proceso"**: te recomiendo definir la diferencia con precisión antes de programarlo — por ejemplo, "En proceso" = alguien la está atendiendo activamente; "Pendiente" = está esperando algo externo (una pieza, una respuesta del solicitante, autorización). Si no se usa así, uno de los dos estados sobra.

**Propuesta final de estados** (catálogo, no hardcodeado — se puede ajustar sin tocar código):
`Registrada → En proceso → Resuelta`, con bifurcaciones a `Pendiente` (esperando algo externo) y `Cancelada`, y la posibilidad de pasar de `Resuelta` a `Reabierta` (que vuelve a comportarse como `En proceso`).

## 4. Catálogos necesarios

Confirmo los que ya tenías: `personal`, `servicios`, `departamentos`, `carreras`, `tipos_atencion`, `tipos_problema`, `estados_atencion`, `periodos_escolares`.

**Importante para evitar duplicar catálogos (como pediste en tu plan):** el catálogo `personal` que ya diseñamos para Documentos (con `personal_rol`) se **reutiliza aquí completo** — solo se le agregan los roles que faltan para la Bitácora: `SECRETARIO`, `PERSONAL_SISTEMAS`, `JEFE_AREA`. Así "Jefe responsable", "Secretario responsable" y "Persona que atendió" salen del mismo catálogo que ya tienen Documentos, sin tabla nueva. `carreras` también se reutiliza tal cual.

## 5. Evidencias (lo que agregaste en tu plan de fases, no estaba en el spec original)

Propongo una tabla de adjuntos, independiente de la atención principal, porque una atención puede tener cero o varias evidencias:

**`atencion_adjuntos`**
| Campo | Tipo | Obligatorio |
|---|---|---|
| id | bigint PK | — |
| atencion_id | bigint FK → atenciones.id | Sí |
| nombre_archivo | varchar(255) | Sí |
| ruta_archivo | varchar(255) | Sí |
| tipo_mime | varchar(100) | Sí |
| tamano_kb | int | Sí |
| usuario_id | bigint FK → users.id (quien lo subió) | Sí |
| fecha_subida | timestamp | Sí |

**Tipos de archivo permitidos:** imágenes (`jpg`, `jpeg`, `png`) para capturas de pantalla/fotos, y `pdf` para documentos de soporte — es lo que normalmente se necesita para evidenciar un problema o una solución. `doc`/`docx`/`xlsx` quedan fuera por ahora porque no aportan como evidencia visual y abren superficie de ataque adicional (archivos ofimáticos con macros); si surge un caso real que los necesite, se habilita después.
**Tamaño máximo:** 5 MB por archivo, sin límite fijo de cantidad de adjuntos por atención (usa buen juicio el usuario, se puede acotar con validación de UI más adelante si se vuelve un problema).

## 6. Seguimiento e historial

Para no perder rastro de cada cambio de estado o comentario interno, separo esto de la atención principal (la atención solo guarda su estado *actual*):

**`atencion_seguimientos`**
| Campo | Tipo | Obligatorio |
|---|---|---|
| id | bigint PK | — |
| atencion_id | bigint FK → atenciones.id | Sí |
| usuario_id | bigint FK → users.id | Sí |
| estado_anterior | relación → estados_atencion.id | No *(vacío en el primer registro)* |
| estado_nuevo | relación → estados_atencion.id | Sí |
| comentario | text | No |
| fecha | timestamp | Sí |

Esto también cubre "historial" del spec original sin duplicar información — el historial completo de una atención es simplemente su lista de `atencion_seguimientos` ordenada por fecha.

## 7. Relaciones

```
personal 1──N atenciones (como jefe, secretario o quien atendió — 3 relaciones distintas a la misma tabla)
servicios / departamentos / tipos_atencion / tipos_problema / estados_atencion 1──N atenciones
atenciones 1──N atencion_adjuntos
atenciones 1──N atencion_seguimientos
users 1──N atenciones (quien registró)
```

---

## Decisiones cerradas de la Fase 3

1. **Matrícula**: confirmado como campo opcional en `atenciones` — solo la tienen los solicitantes que son estudiantes; si el solicitante es personal/docente, queda vacía. No se agrega validación que la obligue.
2. **Estados**: se aprueban `Registrada, En proceso, Pendiente, Resuelta, Cancelada, Reabierta` con la distinción Pendiente (espera algo externo) / En proceso (alguien la trabaja activamente).
3. **Evidencias**: `jpg/jpeg/png/pdf`, máximo 5 MB por archivo, sin límite de cantidad.
4. **Reasignación de "persona que atendió"**: se permite reasignar más de una vez. Por eso `atencion_seguimientos` registra también los cambios de responsable, no solo los de estado — le agrego los campos `responsable_anterior_id` y `responsable_nuevo_id` (ambos FK a `personal`, nullable) a esa tabla para no perder ese historial.

**Fase 3 — Bitácora: cerrada.** Sigue la Fase 4 (Usuarios, Roles y Permisos).
