# Trazabilidad de requisitos

Une cada requisito con el módulo que lo cubre, las tablas que lo sostienen y su estado. Se actualiza al cerrar cada fase.

| Requisito | Módulo | Tablas | Fase | Estado |
|---|---|---|---|---|
| Registrar atenciones con folio único | Bitácora | atenciones, folios_atencion | 9 | Tablas creadas |
| Estados y seguimiento de cada atención | Bitácora | estados_atencion, atencion_seguimientos | 9 | Tablas creadas |
| Evidencias de cada atención | Bitácora | atencion_adjuntos | 9 | Tablas creadas |
| Búsqueda y filtros | Bitácora | Índices en atenciones | 9 y 12 | Índices creados |
| Catálogos (servicios, departamentos, tipos, periodos, carreras) | Catálogos | servicios, departamentos, tipos_atencion, tipos_problema, estados_atencion, periodos_escolares, carreras | 9 | Tablas y datos iniciales |
| Personal, firmantes y destinatarios | Catálogos | personal, personal_rol, roles_documento | 9 y 10 | Tablas y datos iniciales |
| Usuarios, roles y permisos | Seguridad | users, roles, permissions (Spatie) | 8 | Roles y permisos cargados |
| Auditoría de acciones | Seguridad | activity_log | 13 | Tabla creada |
| Estudiantes (datos permanentes) | Estudiantes | estudiantes | 9 | Tabla creada |
| Servicio Social | Trámites | tramites_servicio_social | 9 | Tabla creada |
| Residencias Profesionales | Trámites | tramites_residencia | 9 | Tabla creada |
| Créditos / actividades complementarias | Trámites | tramites_actividad_complementaria | 9 | Tabla creada |
| Seguimiento de estudiantes para el jefe de carrera | Seguimiento | estudiantes + trámites en curso (sin tabla nueva) | 9 | Diseñado; permisos cargados |
| Generar los 5 documentos Word con folio y firmantes | Documentos | tipos_documento, bloques_firma, folios_contador, documentos_generados, documento_firmante | 10 | Tablas creadas; plantillas pendientes |
| Conservar archivo generado y su huella | Documentos | documentos_generados | 10 | Tabla creada |
| Dashboard | Dashboard | Consultas sobre atenciones y documentos | 12 | Pendiente |
| Seguridad (sesiones, validaciones, protección de archivos) | Seguridad | — | 8 y 13 | Pendiente |
| Pruebas | Calidad | tests/ | Todas | Prueba de estructura creada |
| Manuales técnico y de usuario, respaldo y restauración | Documentación | docs/ | Todas | En curso |
| Entradas y salidas del Área de Sistemas; cuántos alumnos y docentes llegan y se atienden por día | Bitácora y Dashboard | atenciones (fecha_hora, hora_salida, tipo_solicitante, estado_id) | 9 y 12 | Campos creados |
