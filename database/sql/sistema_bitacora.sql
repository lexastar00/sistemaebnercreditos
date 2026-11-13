-- =====================================================================
-- Sistema de Gestión y Bitácora del Área de Sistemas — TESI
-- Estructura completa de la base de datos (Fase 5)
-- Motor: MySQL 8 / MariaDB 10.6+  ·  InnoDB  ·  utf8mb4
--
-- Uso en MySQL Workbench:
--   1) File > Open SQL Script... > sistema_bitacora.sql
--   2) Ejecutar todo (rayo).
--   3) Para ver el diagrama: Database > Reverse Engineer... > sistema_bitacora
--
-- Nota: este script sirve para revisar y visualizar el diseño. La base
-- real del proyecto se creará con las migraciones de Laravel.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS sistema_bitacora
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_bitacora;

SET NAMES utf8mb4;

-- =====================================================================
-- 1. CATÁLOGOS
-- =====================================================================

CREATE TABLE carreras (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(150) NOT NULL,
  activo  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE departamentos (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(150) NOT NULL,
  activo  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE servicios (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre           VARCHAR(150) NOT NULL,
  departamento_id  BIGINT UNSIGNED NOT NULL,
  activo           TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_servicios_departamento (departamento_id),
  CONSTRAINT fk_servicios_departamento FOREIGN KEY (departamento_id) REFERENCES departamentos (id)
) ENGINE=InnoDB;

CREATE TABLE tipos_atencion (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(150) NOT NULL,
  activo  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE tipos_problema (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(150) NOT NULL,
  activo  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE estados_atencion (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(50) NOT NULL,
  orden   SMALLINT NOT NULL DEFAULT 0,
  activo  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_estados_nombre (nombre)
) ENGINE=InnoDB;

CREATE TABLE periodos_escolares (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre        VARCHAR(30) NOT NULL,
  fecha_inicio  DATE NOT NULL,
  fecha_fin     DATE NOT NULL,
  activo        TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_periodos_nombre (nombre)
) ENGINE=InnoDB;

CREATE TABLE roles_documento (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave           VARCHAR(50) NOT NULL,
  nombre_visible  VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_documento_clave (clave)
) ENGINE=InnoDB;

-- =====================================================================
-- 2. PERSONAL (destinatarios, firmantes y responsables)
-- =====================================================================

CREATE TABLE personal (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_completo  VARCHAR(200) NOT NULL,
  puesto_actual    VARCHAR(200) NULL,
  departamento_id  BIGINT UNSIGNED NULL,
  activo           TINYINT(1) NOT NULL DEFAULT 1,
  created_at       TIMESTAMP NULL,
  updated_at       TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_personal_departamento (departamento_id),
  CONSTRAINT fk_personal_departamento FOREIGN KEY (departamento_id) REFERENCES departamentos (id)
) ENGINE=InnoDB;

CREATE TABLE personal_rol (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  personal_id  BIGINT UNSIGNED NOT NULL,
  rol_id       BIGINT UNSIGNED NOT NULL,
  activo       TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_personal_rol (personal_id, rol_id),
  CONSTRAINT fk_personal_rol_personal FOREIGN KEY (personal_id) REFERENCES personal (id) ON DELETE CASCADE,
  CONSTRAINT fk_personal_rol_rol      FOREIGN KEY (rol_id)      REFERENCES roles_documento (id)
) ENGINE=InnoDB;

-- =====================================================================
-- 3. SEGURIDAD: USUARIOS, ROLES Y PERMISOS (estructura de Spatie), AUDITORÍA
-- =====================================================================

CREATE TABLE users (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name               VARCHAR(255) NOT NULL,
  email              VARCHAR(255) NOT NULL,
  email_verified_at  TIMESTAMP NULL,
  password           VARCHAR(255) NOT NULL,
  personal_id        BIGINT UNSIGNED NULL,
  activo             TINYINT(1) NOT NULL DEFAULT 1,
  remember_token     VARCHAR(100) NULL,
  created_at         TIMESTAMP NULL,
  updated_at         TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_personal (personal_id),
  CONSTRAINT fk_users_personal FOREIGN KEY (personal_id) REFERENCES personal (id)
) ENGINE=InnoDB;

CREATE TABLE permissions (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(255) NOT NULL,
  guard_name  VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY permissions_name_guard_name_unique (name(150), guard_name(50))
) ENGINE=InnoDB;

CREATE TABLE roles (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(255) NOT NULL,
  guard_name  VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY roles_name_guard_name_unique (name(150), guard_name(50))
) ENGINE=InnoDB;

CREATE TABLE model_has_permissions (
  permission_id  BIGINT UNSIGNED NOT NULL,
  model_type     VARCHAR(255) NOT NULL,
  model_id       BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (permission_id, model_id, model_type(150)),
  KEY model_has_permissions_model_id_model_type_index (model_id, model_type(150)),
  CONSTRAINT fk_mhp_permission FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE model_has_roles (
  role_id     BIGINT UNSIGNED NOT NULL,
  model_type  VARCHAR(255) NOT NULL,
  model_id    BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, model_id, model_type(150)),
  KEY model_has_roles_model_id_model_type_index (model_id, model_type(150)),
  CONSTRAINT fk_mhr_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE role_has_permissions (
  permission_id  BIGINT UNSIGNED NOT NULL,
  role_id        BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (permission_id, role_id),
  KEY idx_rhp_role (role_id),
  CONSTRAINT fk_rhp_permission FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE,
  CONSTRAINT fk_rhp_role       FOREIGN KEY (role_id)       REFERENCES roles (id)       ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activity_log (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  log_name      VARCHAR(255) NULL,
  description   TEXT NOT NULL,
  subject_type  VARCHAR(255) NULL,
  event         VARCHAR(255) NULL,
  subject_id    BIGINT UNSIGNED NULL,
  causer_type   VARCHAR(255) NULL,
  causer_id     BIGINT UNSIGNED NULL,
  properties    JSON NULL,
  batch_uuid    CHAR(36) NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY activity_log_log_name_index (log_name(100)),
  KEY subject (subject_type(100), subject_id),
  KEY causer (causer_type(100), causer_id)
) ENGINE=InnoDB;

-- =====================================================================
-- 4. ESTUDIANTES Y TRÁMITES
-- =====================================================================

CREATE TABLE estudiantes (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_completo       VARCHAR(200) NOT NULL,
  matricula             VARCHAR(20) NOT NULL,
  carrera_id            BIGINT UNSIGNED NOT NULL,
  semestre              TINYINT UNSIGNED NULL,
  correo_institucional  VARCHAR(150) NULL,
  telefono              VARCHAR(20) NULL,
  periodo_id            BIGINT UNSIGNED NULL,
  estatus               ENUM('activo','egresado','baja') NOT NULL DEFAULT 'activo',
  created_at            TIMESTAMP NULL,
  updated_at            TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_estudiantes_matricula (matricula),
  KEY idx_estudiantes_nombre (nombre_completo),
  KEY idx_estudiantes_carrera (carrera_id),
  KEY idx_estudiantes_periodo (periodo_id),
  CONSTRAINT fk_estudiantes_carrera FOREIGN KEY (carrera_id) REFERENCES carreras (id),
  CONSTRAINT fk_estudiantes_periodo FOREIGN KEY (periodo_id) REFERENCES periodos_escolares (id)
) ENGINE=InnoDB;

CREATE TABLE tramites_residencia (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  estudiante_id       BIGINT UNSIGNED NOT NULL,
  proyecto            VARCHAR(255) NOT NULL,
  empresa_institucion VARCHAR(255) NOT NULL,
  asesor_interno_id   BIGINT UNSIGNED NULL,
  asesor_externo      VARCHAR(200) NULL,
  periodo_id          BIGINT UNSIGNED NULL,
  fecha_inicio        DATE NOT NULL,
  fecha_termino       DATE NOT NULL,
  horas               SMALLINT UNSIGNED NOT NULL,
  estado              ENUM('en_curso','terminado','cancelado') NOT NULL DEFAULT 'en_curso',
  created_at          TIMESTAMP NULL,
  updated_at          TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_tres_estudiante (estudiante_id, estado),
  KEY idx_tres_asesor (asesor_interno_id),
  KEY idx_tres_periodo (periodo_id),
  CONSTRAINT fk_tres_estudiante FOREIGN KEY (estudiante_id)     REFERENCES estudiantes (id),
  CONSTRAINT fk_tres_asesor     FOREIGN KEY (asesor_interno_id) REFERENCES personal (id),
  CONSTRAINT fk_tres_periodo    FOREIGN KEY (periodo_id)        REFERENCES periodos_escolares (id)
) ENGINE=InnoDB;

CREATE TABLE tramites_servicio_social (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  estudiante_id         BIGINT UNSIGNED NOT NULL,
  institucion_receptora VARCHAR(255) NOT NULL,
  programa_actividad    VARCHAR(255) NULL,
  periodo_id            BIGINT UNSIGNED NULL,
  fecha_inicio          DATE NOT NULL,
  fecha_termino         DATE NOT NULL,
  horas                 SMALLINT UNSIGNED NOT NULL,
  estado                ENUM('en_curso','terminado','cancelado') NOT NULL DEFAULT 'en_curso',
  created_at            TIMESTAMP NULL,
  updated_at            TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_tss_estudiante (estudiante_id, estado),
  KEY idx_tss_periodo (periodo_id),
  CONSTRAINT fk_tss_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes (id),
  CONSTRAINT fk_tss_periodo    FOREIGN KEY (periodo_id)    REFERENCES periodos_escolares (id)
) ENGINE=InnoDB;

CREATE TABLE tramites_actividad_complementaria (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  estudiante_id     BIGINT UNSIGNED NOT NULL,
  tipo_actividad    VARCHAR(100) NOT NULL,
  nombre_actividad  VARCHAR(255) NOT NULL,
  periodo_escolar   VARCHAR(20) NOT NULL,
  creditos          DECIMAL(3,1) NOT NULL,
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_tac_estudiante (estudiante_id),
  CONSTRAINT fk_tac_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes (id)
) ENGINE=InnoDB;

-- =====================================================================
-- 5. DOCUMENTOS
-- =====================================================================

CREATE TABLE tipos_documento (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave              VARCHAR(20) NOT NULL,
  nombre             VARCHAR(150) NOT NULL,
  prefijo_folio      VARCHAR(20) NOT NULL,
  ruta_plantilla     VARCHAR(255) NOT NULL,
  version_plantilla  INT UNSIGNED NOT NULL DEFAULT 1,
  activo             TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tipos_documento_clave (clave)
) ENGINE=InnoDB;

CREATE TABLE bloques_firma (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo_documento_id  BIGINT UNSIGNED NOT NULL,
  rol_id             BIGINT UNSIGNED NOT NULL,
  etiqueta           VARCHAR(100) NOT NULL,
  orden              SMALLINT NOT NULL,
  obligatorio        TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_bloques_tipo (tipo_documento_id),
  KEY idx_bloques_rol (rol_id),
  CONSTRAINT fk_bloques_tipo FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento (id),
  CONSTRAINT fk_bloques_rol  FOREIGN KEY (rol_id)            REFERENCES roles_documento (id)
) ENGINE=InnoDB;

CREATE TABLE folios_contador (
  tipo_documento_id  BIGINT UNSIGNED NOT NULL,
  anio               SMALLINT UNSIGNED NOT NULL,
  ultimo_numero      INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (tipo_documento_id, anio),
  CONSTRAINT fk_folios_tipo FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento (id)
) ENGINE=InnoDB;

CREATE TABLE documentos_generados (
  id                       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  folio                    VARCHAR(30) NOT NULL,
  tipo_documento_id        BIGINT UNSIGNED NOT NULL,
  estudiante_id            BIGINT UNSIGNED NOT NULL,
  tramite_type             VARCHAR(100) NOT NULL,
  tramite_id               BIGINT UNSIGNED NOT NULL,
  version_plantilla_usada  INT UNSIGNED NOT NULL,
  fecha_generacion         DATETIME NOT NULL,
  usuario_id               BIGINT UNSIGNED NOT NULL,
  nombre_archivo           VARCHAR(255) NOT NULL,
  ruta_archivo             VARCHAR(255) NOT NULL,
  hash_archivo             CHAR(64) NULL,
  created_at               TIMESTAMP NULL,
  updated_at               TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_documentos_folio (folio),
  KEY idx_docs_tipo (tipo_documento_id),
  KEY idx_docs_estudiante (estudiante_id),
  KEY idx_docs_tramite (tramite_type, tramite_id),
  KEY idx_docs_usuario (usuario_id),
  KEY idx_docs_fecha (fecha_generacion),
  CONSTRAINT fk_docs_tipo       FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento (id),
  CONSTRAINT fk_docs_estudiante FOREIGN KEY (estudiante_id)     REFERENCES estudiantes (id),
  CONSTRAINT fk_docs_usuario    FOREIGN KEY (usuario_id)        REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE documento_firmante (
  id                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  documento_generado_id  BIGINT UNSIGNED NOT NULL,
  bloque_firma_id        BIGINT UNSIGNED NOT NULL,
  personal_id            BIGINT UNSIGNED NOT NULL,
  nombre_snapshot        VARCHAR(200) NOT NULL,
  puesto_snapshot        VARCHAR(200) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_df_documento (documento_generado_id),
  KEY idx_df_bloque (bloque_firma_id),
  KEY idx_df_personal (personal_id),
  CONSTRAINT fk_df_documento FOREIGN KEY (documento_generado_id) REFERENCES documentos_generados (id) ON DELETE CASCADE,
  CONSTRAINT fk_df_bloque    FOREIGN KEY (bloque_firma_id)       REFERENCES bloques_firma (id),
  CONSTRAINT fk_df_personal  FOREIGN KEY (personal_id)           REFERENCES personal (id)
) ENGINE=InnoDB;

-- =====================================================================
-- 6. BITÁCORA DE ATENCIONES
-- =====================================================================

CREATE TABLE folios_atencion (
  anio           SMALLINT UNSIGNED NOT NULL,
  ultimo_numero  INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (anio)
) ENGINE=InnoDB;

CREATE TABLE atenciones (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  folio                 VARCHAR(20) NOT NULL,
  fecha_hora            DATETIME NOT NULL,
  tipo_solicitante      ENUM('alumno','docente','administrativo','otro') NOT NULL DEFAULT 'alumno',
  hora_salida           DATETIME NULL,
  solicitante_nombre    VARCHAR(200) NOT NULL,
  matricula             VARCHAR(20) NULL,
  estudiante_id         BIGINT UNSIGNED NULL,
  asunto                VARCHAR(255) NOT NULL,
  jefe_id               BIGINT UNSIGNED NOT NULL,
  secretario_id         BIGINT UNSIGNED NOT NULL,
  atendido_por_id       BIGINT UNSIGNED NULL,
  servicio_id           BIGINT UNSIGNED NOT NULL,
  tipo_atencion_id      BIGINT UNSIGNED NOT NULL,
  tipo_problema_id      BIGINT UNSIGNED NULL,
  descripcion_problema  TEXT NOT NULL,
  solucion              TEXT NULL,
  observaciones         TEXT NULL,
  estado_id             BIGINT UNSIGNED NOT NULL,
  usuario_registro_id   BIGINT UNSIGNED NOT NULL,
  created_at            TIMESTAMP NULL,
  updated_at            TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_atenciones_folio (folio),
  KEY idx_atenciones_fecha (fecha_hora),
  KEY idx_atenciones_estado (estado_id),
  KEY idx_atenciones_servicio (servicio_id),
  KEY idx_atenciones_atendio (atendido_por_id),
  KEY idx_atenciones_matricula (matricula),
  KEY idx_atenciones_estudiante (estudiante_id),
  KEY idx_atenciones_jefe (jefe_id),
  KEY idx_atenciones_secretario (secretario_id),
  KEY idx_atenciones_tipo_atencion (tipo_atencion_id),
  KEY idx_atenciones_tipo_problema (tipo_problema_id),
  KEY idx_atenciones_usuario (usuario_registro_id),
  CONSTRAINT fk_atenciones_estudiante    FOREIGN KEY (estudiante_id)       REFERENCES estudiantes (id),
  CONSTRAINT fk_atenciones_jefe          FOREIGN KEY (jefe_id)             REFERENCES personal (id),
  CONSTRAINT fk_atenciones_secretario    FOREIGN KEY (secretario_id)       REFERENCES personal (id),
  CONSTRAINT fk_atenciones_atendio       FOREIGN KEY (atendido_por_id)     REFERENCES personal (id),
  CONSTRAINT fk_atenciones_servicio      FOREIGN KEY (servicio_id)         REFERENCES servicios (id),
  CONSTRAINT fk_atenciones_tipo_atencion FOREIGN KEY (tipo_atencion_id)    REFERENCES tipos_atencion (id),
  CONSTRAINT fk_atenciones_tipo_problema FOREIGN KEY (tipo_problema_id)    REFERENCES tipos_problema (id),
  CONSTRAINT fk_atenciones_estado        FOREIGN KEY (estado_id)           REFERENCES estados_atencion (id),
  CONSTRAINT fk_atenciones_usuario       FOREIGN KEY (usuario_registro_id) REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE atencion_adjuntos (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  atencion_id     BIGINT UNSIGNED NOT NULL,
  nombre_archivo  VARCHAR(255) NOT NULL,
  ruta_archivo    VARCHAR(255) NOT NULL,
  tipo_mime       VARCHAR(100) NOT NULL,
  tamano_kb       INT UNSIGNED NOT NULL,
  usuario_id      BIGINT UNSIGNED NOT NULL,
  fecha_subida    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_adj_atencion (atencion_id),
  KEY idx_adj_usuario (usuario_id),
  CONSTRAINT fk_adj_atencion FOREIGN KEY (atencion_id) REFERENCES atenciones (id) ON DELETE CASCADE,
  CONSTRAINT fk_adj_usuario  FOREIGN KEY (usuario_id)  REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE atencion_seguimientos (
  id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  atencion_id             BIGINT UNSIGNED NOT NULL,
  usuario_id              BIGINT UNSIGNED NOT NULL,
  estado_anterior_id      BIGINT UNSIGNED NULL,
  estado_nuevo_id         BIGINT UNSIGNED NOT NULL,
  responsable_anterior_id BIGINT UNSIGNED NULL,
  responsable_nuevo_id    BIGINT UNSIGNED NULL,
  comentario              TEXT NULL,
  fecha                   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_seg_atencion (atencion_id, fecha),
  KEY idx_seg_usuario (usuario_id),
  KEY idx_seg_est_ant (estado_anterior_id),
  KEY idx_seg_est_nue (estado_nuevo_id),
  KEY idx_seg_resp_ant (responsable_anterior_id),
  KEY idx_seg_resp_nue (responsable_nuevo_id),
  CONSTRAINT fk_seg_atencion FOREIGN KEY (atencion_id)             REFERENCES atenciones (id) ON DELETE CASCADE,
  CONSTRAINT fk_seg_usuario  FOREIGN KEY (usuario_id)              REFERENCES users (id),
  CONSTRAINT fk_seg_est_ant  FOREIGN KEY (estado_anterior_id)      REFERENCES estados_atencion (id),
  CONSTRAINT fk_seg_est_nue  FOREIGN KEY (estado_nuevo_id)         REFERENCES estados_atencion (id),
  CONSTRAINT fk_seg_resp_ant FOREIGN KEY (responsable_anterior_id) REFERENCES personal (id),
  CONSTRAINT fk_seg_resp_nue FOREIGN KEY (responsable_nuevo_id)    REFERENCES personal (id)
) ENGINE=InnoDB;

-- =====================================================================
-- 7. DATOS INICIALES
-- =====================================================================

INSERT INTO carreras (nombre) VALUES ('Ingeniería en Sistemas Computacionales');

INSERT INTO estados_atencion (nombre, orden) VALUES
  ('Registrada', 1), ('En proceso', 2), ('Pendiente', 3),
  ('Resuelta', 4), ('Cancelada', 5), ('Reabierta', 6);

INSERT INTO roles_documento (clave, nombre_visible) VALUES
  ('JEFE_DIVISION',               'Jefe de División'),
  ('DOCENTE',                     'Docente'),
  ('SUBDIRECCION_ACADEMICA',      'Subdirección académica'),
  ('DESTINATARIO_SS_RESIDENCIAS', 'Destinatario de cartas de Servicio Social y Residencias'),
  ('DESTINATARIO_CONSTANCIA',     'Destinatario de constancias'),
  ('SECRETARIO',                  'Secretario'),
  ('PERSONAL_SISTEMAS',           'Personal de Sistemas'),
  ('JEFE_AREA',                   'Jefe de Área');

INSERT INTO tipos_documento (clave, nombre, prefijo_folio, ruta_plantilla) VALUES
  ('RES_ACEP', 'Carta de Aceptación de Residencia Profesional', 'ISC-RES-ACEP', 'plantillas/carta_aceptacion_residencia.docx'),
  ('RES_TERM', 'Carta de Término de Residencia Profesional',    'ISC-RES-TERM', 'plantillas/carta_termino_residencia.docx'),
  ('SS_ACEP',  'Carta de Aceptación de Servicio Social',        'ISC-SS-ACEP',  'plantillas/carta_aceptacion_servicio_social.docx'),
  ('SS_TERM',  'Carta de Término de Servicio Social',           'ISC-SS-TERM',  'plantillas/carta_termino_servicio_social.docx'),
  ('CTA',      'Constancia de Acreditación de Actividades Complementarias', 'ISC-CTA', 'plantillas/constancia_actividades_complementarias.docx');

-- Bloques de firma / destinatario por tipo de documento
INSERT INTO bloques_firma (tipo_documento_id, rol_id, etiqueta, orden, obligatorio)
SELECT t.id, r.id, 'Destinatario', 1, 1
  FROM tipos_documento t JOIN roles_documento r ON r.clave = 'DESTINATARIO_SS_RESIDENCIAS'
 WHERE t.clave IN ('RES_ACEP','RES_TERM','SS_ACEP','SS_TERM');

INSERT INTO bloques_firma (tipo_documento_id, rol_id, etiqueta, orden, obligatorio)
SELECT t.id, r.id, 'Firma — Jefe de División', 2, 1
  FROM tipos_documento t JOIN roles_documento r ON r.clave = 'JEFE_DIVISION'
 WHERE t.clave IN ('RES_ACEP','RES_TERM','SS_ACEP','SS_TERM','CTA');

INSERT INTO bloques_firma (tipo_documento_id, rol_id, etiqueta, orden, obligatorio)
SELECT t.id, r.id, 'Destinatario', 1, 1
  FROM tipos_documento t JOIN roles_documento r ON r.clave = 'DESTINATARIO_CONSTANCIA'
 WHERE t.clave = 'CTA';

INSERT INTO bloques_firma (tipo_documento_id, rol_id, etiqueta, orden, obligatorio)
SELECT t.id, r.id, 'Vo.Bo. — Docente', 3, 1
  FROM tipos_documento t JOIN roles_documento r ON r.clave = 'DOCENTE'
 WHERE t.clave = 'CTA';

INSERT INTO bloques_firma (tipo_documento_id, rol_id, etiqueta, orden, obligatorio)
SELECT t.id, r.id, 'Vo.Bo. — Subdirección académica', 4, 1
  FROM tipos_documento t JOIN roles_documento r ON r.clave = 'SUBDIRECCION_ACADEMICA'
 WHERE t.clave = 'CTA';

-- Roles y permisos (Spatie) según la matriz de la Fase 4
INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES
  ('Administrador',       'web', NOW(), NOW()),
  ('Jefe de Área',        'web', NOW(), NOW()),
  ('Secretario',          'web', NOW(), NOW()),
  ('Personal de Sistemas','web', NOW(), NOW()),
  ('Usuario de Consulta', 'web', NOW(), NOW());

INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
  ('bitacora.ver','web',NOW(),NOW()), ('bitacora.crear','web',NOW(),NOW()),
  ('bitacora.editar','web',NOW(),NOW()), ('bitacora.cambiar_estado','web',NOW(),NOW()),
  ('bitacora.reasignar','web',NOW(),NOW()), ('bitacora.cancelar','web',NOW(),NOW()),
  ('bitacora.adjuntar_evidencia','web',NOW(),NOW()),
  ('documentos.generar','web',NOW(),NOW()), ('documentos.ver_historial','web',NOW(),NOW()),
  ('documentos.descargar','web',NOW(),NOW()),
  ('estudiantes.ver','web',NOW(),NOW()), ('estudiantes.crear','web',NOW(),NOW()),
  ('estudiantes.editar','web',NOW(),NOW()),
  ('seguimiento.ver','web',NOW(),NOW()), ('seguimiento.ver_contacto','web',NOW(),NOW()),
  ('catalogos.administrar','web',NOW(),NOW()), ('usuarios.administrar','web',NOW(),NOW()),
  ('auditoria.ver','web',NOW(),NOW()), ('configuracion.administrar','web',NOW(),NOW()),
  ('dashboard.ver_global','web',NOW(),NOW()), ('dashboard.ver_departamento','web',NOW(),NOW());

-- Administrador: todos los permisos
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id FROM permissions p JOIN roles r ON r.name = 'Administrador';

-- Jefe de Área
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id FROM permissions p JOIN roles r ON r.name = 'Jefe de Área'
 WHERE p.name IN ('bitacora.ver','bitacora.crear','bitacora.editar','bitacora.cambiar_estado',
                  'bitacora.reasignar','bitacora.cancelar','bitacora.adjuntar_evidencia',
                  'documentos.generar','documentos.ver_historial','documentos.descargar',
                  'estudiantes.ver','estudiantes.crear','estudiantes.editar',
                  'seguimiento.ver','seguimiento.ver_contacto','dashboard.ver_departamento');

-- Secretario
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id FROM permissions p JOIN roles r ON r.name = 'Secretario'
 WHERE p.name IN ('bitacora.ver','bitacora.crear','bitacora.editar','bitacora.adjuntar_evidencia',
                  'documentos.generar','documentos.ver_historial','documentos.descargar',
                  'estudiantes.ver','estudiantes.crear','estudiantes.editar');

-- Personal de Sistemas
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id FROM permissions p JOIN roles r ON r.name = 'Personal de Sistemas'
 WHERE p.name IN ('bitacora.ver','bitacora.editar','bitacora.cambiar_estado','bitacora.adjuntar_evidencia');

-- Usuario de Consulta (solo lectura; ve ubicación pero no contacto)
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id FROM permissions p JOIN roles r ON r.name = 'Usuario de Consulta'
 WHERE p.name IN ('bitacora.ver','documentos.ver_historial','estudiantes.ver',
                  'seguimiento.ver','dashboard.ver_global');

-- Valores de EJEMPLO (editables hasta tener la lista real del área)
INSERT INTO departamentos (nombre) VALUES ('Área de Sistemas'), ('División de Ingeniería en Sistemas Computacionales');
INSERT INTO servicios (nombre, departamento_id)
  SELECT 'Soporte técnico', id FROM departamentos WHERE nombre = 'Área de Sistemas';
INSERT INTO servicios (nombre, departamento_id)
  SELECT 'Cuentas y accesos', id FROM departamentos WHERE nombre = 'Área de Sistemas';
INSERT INTO tipos_atencion (nombre) VALUES ('Soporte'), ('Instalación'), ('Asesoría'), ('Trámite');
INSERT INTO tipos_problema (nombre) VALUES ('Hardware'), ('Software'), ('Red'), ('Cuenta de usuario');
