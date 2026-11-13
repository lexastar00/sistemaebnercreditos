# Sistema de Gestión y Bitácora del Área de Sistemas
### Análisis y propuesta técnica — Etapa 1 (sin código)

---

## 1. Resumen de lo entendido

El proyecto tiene dos frentes que conviven en un mismo sistema:

1. **Bitácora de atención**: registrar, dar seguimiento y consultar las solicitudes que recibe el Área de Sistemas (folio único, responsables, problema, solución, estado).
2. **Generación de documentos oficiales**: usar machotes Word reales (constancias de créditos, Servicio Social, cartas de aceptación de Servicio Social y Residencias) y llenarlos automáticamente a partir de datos capturados o ya existentes en el sistema (estudiantes, Servicio Social, Residencias).

Ambos frentes comparten un núcleo común: catálogos, usuarios/roles, auditoría y un módulo central de estudiantes que evita capturar los mismos datos varias veces. El desarrollo será progresivo, módulo por módulo, sobre Laravel, sin tocar el diseño de los machotes (eso lo defines tú cuando los compartas).

---

## 2. Módulos propuestos

| # | Módulo | Función |
|---|--------|---------|
| 1 | **Autenticación y Usuarios** | Login, roles, permisos, perfiles |
| 2 | **Catálogos** | Personal, servicios, departamentos, carreras, tipos de atención/problema, estados, periodos escolares |
| 3 | **Bitácora de Atención** | Núcleo del sistema: folios, registro, seguimiento, búsqueda |
| 4 | **Estudiantes** | Registro central: matrícula, carrera, semestre, estatus |
| 5 | **Servicio Social** | Seguimiento de procesos de Servicio Social por estudiante |
| 6 | **Residencias Profesionales** | Seguimiento de residencias por estudiante |
| 7 | **Créditos** | Cálculo/registro de créditos para constancias |
| 8 | **Documentos** | Generación de .docx a partir de machotes + plantillas de datos |
| 9 | **Auditoría** | Bitácora de acciones del sistema (quién, qué, cuándo) |
| 10 | **Dashboard** | Indicadores clave, sin saturar de gráficas |
| 11 | **Configuración** | Parámetros generales (formato de folio, periodo activo, etc.) |

No propongo módulos adicionales por ahora — los 11 anteriores cubren todo lo descrito sin inventar alcance.

---

## 3. Roles y permisos

Tus 5 roles están bien pensados, pero los estructuraría como **roles + permisos granulares** (no permisos fijos por rol), usando algo como `spatie/laravel-permission`. Esto te permite ajustar accesos sin tocar código cuando cambien las necesidades del área.

**Roles base:**

- **Administrador** — gestión total: usuarios, roles, catálogos, configuración, ve toda la auditoría.
- **Jefe de Área** — visibilidad completa de bitácora y reportes de su área; puede reasignar atenciones; no administra catálogos globales.
- **Secretario** — captura atenciones, genera documentos, consulta estudiantes; sin acceso a configuración ni auditoría.
- **Personal de Sistemas (Técnico)** — atiende y actualiza únicamente las solicitudes que tiene asignadas o las de su departamento.
- **Usuario de Consulta** — solo lectura, con alcance limitado (por ejemplo, un jefe de carrera que solo ve lo de su carrera).

**Sugerencia adicional (a validar contigo):** un permiso separado de "solo auditoría" para que, si algún día se requiere una revisión externa (control interno, dirección), se pueda dar acceso de solo lectura al historial sin exponer el resto del sistema. No lo agrego como rol nuevo — sería un permiso que se puede asignar a cualquier rol.

---

## 4. Arquitectura propuesta

**Backend**
- Laravel (última versión estable — a confirmar contigo, ver sección 10).
- Patrón MVC estándar + capa de **Services** para lógica de negocio (folios, generación de documentos), manteniendo los controladores delgados.
- `spatie/laravel-permission` → roles y permisos.
- `spatie/laravel-activitylog` → auditoría (evita reinventar una bitácora de auditoría desde cero).
- `phpoffice/phpword` → generación/relleno de documentos Word a partir de plantillas.
- Laravel Breeze o Fortify para el scaffolding de autenticación (login, throttling de intentos, recuperación de contraseña) — más ligero que Jetstream si no necesitas API tokens todavía.

**Frontend**
- Blade + Alpine.js + Tailwind CSS para una interfaz moderna y responsive sin la complejidad de un framework JS separado.
- Si prefieres más interactividad tipo SPA (formularios dinámicos, búsquedas en vivo sin recargar), Livewire encaja bien con Laravel sin necesidad de una API separada. Te lo dejo como decisión abierta (sección 10).

**Base de datos**
- MySQL o MariaDB (relacional, ampliamente soportado en hosting institucional).

**Por qué estas librerías y no otras:** todas son del ecosistema oficial de Laravel o son el estándar de facto (Spatie es el paquete más usado y mantenido para permisos y auditoría en Laravel; PHPWord es la única librería PHP madura para generar/editar .docx). No propongo frameworks JS adicionales, ni ORMs alternos, ni librerías de UI pesadas — se ajusta a "no agregar dependencias innecesarias".

---

## 5. Estructura inicial de base de datos (propuesta, sujeta a ajuste)

**Catálogos**
- `personal` (id, nombre, puesto, departamento_id, activo)
- `servicios` (id, nombre)
- `departamentos` (id, nombre)
- `carreras` (id, nombre, clave)
- `tipos_atencion` (id, nombre)
- `tipos_problema` (id, nombre)
- `estados_atencion` (id, nombre, orden) — catálogo, no hardcodeado, para poder ajustar estados sin tocar código
- `periodos_escolares` (id, nombre, fecha_inicio, fecha_fin, activo)

**Núcleo**
- `estudiantes` (id, nombre, matricula [único], carrera_id, semestre, correo_institucional, telefono, periodo_id, estatus)
- `atenciones` (id, folio [único], fecha, hora, solicitante_nombre, matricula, estudiante_id [nullable, FK], asunto, jefe_id, secretario_id, atendido_por_id, servicio_id, tipo_atencion_id, tipo_problema_id, descripcion_problema, solucion, observaciones, estado_id, usuario_registro_id, created_at, updated_at)
- `documentos_generados` (id, tipo_documento, folio_referencia, estudiante_id [nullable], usuario_id, nombre_archivo, fecha_generacion, ruta_o_hash)
- `servicio_social` (id, estudiante_id, institucion, programa, periodo_id, fecha_inicio, fecha_fin, horas, responsable_id, estado)
- `residencias_profesionales` (id, estudiante_id, empresa, proyecto, asesor_interno_id, asesor_externo, periodo_id, fecha_inicio, fecha_fin, estado)

**Sistema**
- `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions` (generadas por Spatie)
- `activity_log` (generada por Spatie)

**Relaciones clave:** `atenciones` referencia catálogos y personal por FK; `estudiantes` es el punto central del que cuelgan Servicio Social, Residencias y Documentos; el folio se genera con un contador atómico (transacción + bloqueo de fila o secuencia por año) para evitar duplicados bajo concurrencia.

No incluyo tablas para "tipos de créditos" o similar hasta ver el machote real — evito inventar campos, como pediste.

---

## 6. Flujo general del sistema

**Bitácora:**
1. Solicitante llega/llama → Secretario o Personal de Sistemas abre nueva atención.
2. Sistema genera folio único automáticamente (`SIS-2026-000001`).
3. Se captura solicitante, motivo, responsables, departamento/servicio.
4. Estado inicial: **Registrada**.
5. Personal de Sistemas la atiende → actualiza descripción, solución, cambia estado (**En proceso** → **Resuelta**, o **Pendiente**/**Cancelada** según el caso).
6. Todo cambio queda en auditoría con usuario, fecha/hora y campo modificado.
7. Consulta posterior vía folio, nombre, matrícula, fechas, responsable, departamento o estado.

**Documentos:**
1. Usuario autorizado elige tipo de documento (constancia, carta de aceptación, etc.).
2. Si el estudiante ya existe en el sistema (o tiene Servicio Social/Residencia registrada), el formulario se prellena; si no, se captura manualmente.
3. Sistema toma la plantilla correspondiente y sustituye los campos variables conservando el formato original.
4. Se genera el .docx, se descarga, y se registra en `documentos_generados` (metadato, no necesariamente el archivo completo — ver sección 8).
5. El documento se imprime y firma físicamente fuera del sistema.

---

## 7. Estrategia de seguridad

Todo lo que listaste ya está cubierto de forma nativa o casi nativa en Laravel; aquí cómo se resuelve cada punto:

- **CSRF**: middleware `VerifyCsrfToken`, activo por defecto en formularios Blade.
- **SQL Injection**: Eloquent/Query Builder con bindings parametrizados — nunca SQL crudo concatenado.
- **XSS**: escape automático de Blade (`{{ }}`), y sanitización explícita en cualquier campo que se muestre como HTML.
- **Contraseñas**: hash con `bcrypt` (o `argon2id` si el hosting lo soporta).
- **Fuerza bruta / rate limiting**: throttling de login vía Fortify/Breeze + middleware `throttle` en rutas sensibles.
- **Sesiones**: cookies `secure`, `httpOnly`, `SameSite=Lax`, regeneración de ID de sesión tras login.
- **Autorización**: Policies de Laravel + permisos de Spatie en cada acción (no solo ocultar botones en la vista — validar también en el controlador).
- **Validación de archivos**: `mimes:docx`, límite de tamaño y validación de extensión real (no solo el nombre) en cualquier carga de machotes.
- **Auditoría**: `spatie/laravel-activitylog` en modelos y eventos de autenticación.
- **Manejo de errores**: `APP_DEBUG=false` en producción, páginas de error genéricas, logs internos sin exponer detalles al usuario.
- **Variables de entorno**: credenciales y llaves en `.env`, nunca en el repositorio.
- **Datos personales**: acceso a `estudiantes` limitado por rol/permiso; considerar cifrado a nivel de campo para datos sensibles si la institución lo exige (a confirmar).

**Punto adicional que propongo añadir:** política de expiración/renovación de sesión por inactividad, dado que varios roles comparten equipos en un área administrativa (riesgo de sesión abierta en equipo compartido).

---

## 8. Estrategia para generación de documentos Word

1. Cuando compartas cada machote, lo analizo para identificar texto fijo vs. campos variables (tal como pediste, sin inventar diseño).
2. Preparo una copia del machote con marcadores de campo (`${nombre_estudiante}`, `${matricula}`, etc.) usando el enfoque de **TemplateProcessor** de PHPWord, que permite sustituir texto dentro de un .docx **sin reconstruir el documento**, conservando fuente, tablas, encabezados y pies de página originales.
3. Cada tipo de documento queda mapeado a: (a) el archivo plantilla, (b) los campos que requiere, (c) de dónde se obtiene cada dato (captura manual vs. tomado de `estudiantes`/`servicio_social`/`residencias_profesionales`).
4. Generación bajo demanda: el sistema arma el .docx en el momento de la solicitud y lo entrega para descarga.
5. **Sobre almacenamiento** (tu pregunta en el punto 18): recomiendo guardar solo el **metadato** en `documentos_generados` (quién, cuándo, qué tipo, folio/estudiante relacionado) y no conservar permanentemente cada archivo generado, salvo que la institución requiera un respaldo histórico de documentos emitidos. Si lo requieren, se puede guardar el archivo en `storage/app/documentos` con retención definida — te pregunto esto en la sección 10 porque cambia el diseño de esa tabla.

---

## 9. Estrategia de Git

- `main` — siempre desplegable, solo recibe merges desde `develop` mediante versiones etiquetadas (`v1.0.0`, `v1.1.0`...).
- `develop` — integración de módulos ya probados.
- `feature/<módulo>` — una rama por módulo (`feature/bitacora`, `feature/catalogos`, `feature/documentos`, `feature/estudiantes`, etc.), se abre al iniciar el módulo y se fusiona a `develop` al cerrarlo.
- `hotfix/<descripción>` — correcciones urgentes directo sobre `main`, luego reincorporadas a `develop`.
- Commits siguiendo convención simple: `feat:`, `fix:`, `refactor:`, `docs:`, `test:` — facilita generar el changelog más adelante y ubicar qué cambió y por qué.

---

## 10. Información que todavía necesito

1. **Versión de Laravel** que prefieres usar (o si debo proponer la última estable).
2. **Hosting**: ¿VPS con acceso a composer/artisan, o hosting compartido con limitaciones? Esto define si Livewire es viable o si conviene algo más ligero.
3. **Frontend**: ¿Blade + Alpine tradicional, o prefieres Livewire para más interactividad (formularios dinámicos, búsquedas en vivo)?
4. **Autenticación institucional**: ¿login local (usuario/contraseña propios del sistema) o necesitas integrarlo con un directorio institucional (LDAP/Active Directory/SSO) más adelante?
5. **Formato exacto del folio**: ¿se reinicia cada año escolar, cada periodo, o es continuo? ¿Longitud fija de 6 dígitos siempre?
6. **Retención de documentos generados**: ¿solo metadato, o se debe guardar copia del archivo por tiempo definido?
7. Los **machotes Word reales** (créditos, Servicio Social, cartas de aceptación) cuando los tengas disponibles — es el insumo que más condiciona el módulo de Documentos.
8. **Volumen esperado**: aproximado de atenciones por día/semana, para dimensionar si algo como búsqueda full-text (Laravel Scout) se justifica desde ahora o se deja para después.
9. **Identidad visual**: ¿existe un logo/colores institucionales que deba reflejar la interfaz, o defino una paleta profesional neutra?

---

## 11. Posibles problemas o riesgos

- **Alcance amplio** (11 módulos): mitigado por el desarrollo modular que ya pediste — el riesgo real está en no cerrar un módulo antes de abrir el siguiente.
- **Concurrencia en folios**: dos atenciones capturadas al mismo tiempo podrían generar el mismo folio si no se controla con transacción/bloqueo — se resuelve a nivel de diseño desde el inicio, no después.
- **Fidelidad del formato Word**: PHPWord con TemplateProcessor conserva bien el formato en la mayoría de los casos, pero machotes con tablas muy complejas o campos que cruzan varios "runs" de texto a veces requieren ajuste manual de la plantilla antes de insertar los marcadores. Lo evaluamos documento por documento cuando los compartas.
- **Datos personales de estudiantes**: al centralizar esta información, el control de acceso por rol deja de ser opcional — un error de permisos expone datos sensibles de muchos estudiantes a la vez.
- **Dependencia de un solo desarrollador**: si el sistema queda a tu cargo, la documentación técnica (sección 26) no es opcional — es lo que evita que el sistema quede "atrapado" en tu conocimiento.

---

## 12. Mejoras que considero útiles (a validar, no implicadas por lo que pediste)

- **Notificación interna** (correo o en-sistema) cuando se asigna una atención a Personal de Sistemas, para no depender de que alguien revise el dashboard.
- **Exportación de bitácora a Excel/CSV** para reportes que la Jefatura pueda usar fuera del sistema.
- **Backups automatizados** de base de datos (independiente del sistema, pero relevante mencionarlo dado que maneja datos institucionales).
- Dejar la puerta abierta a una **API interna** (Sanctum) más adelante, sin construirla ahora, por si en el futuro se requiere una app o integración externa.

No agrego gráficas ni módulos extra al dashboard — coincido con tu criterio de evitar visualizaciones innecesarias.

---

Quedo a la espera de tus respuestas a la sección 10 y de los machotes Word para poder cerrar el diseño del módulo de Documentos. En cuanto confirmes, seguimos con el primer módulo a implementar.
