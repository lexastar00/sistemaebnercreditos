# Arquitectura y tecnologías

## Tecnologías

| Tecnología | Para qué sirve en este sistema | Por qué se eligió |
|---|---|---|
| PHP 8.3 | Lenguaje en el que está escrito el sistema | Requerido por Laravel 13; estándar en hosting institucional |
| Laravel 13 | Framework: rutas, validación, autenticación, acceso a base de datos, seguridad | Trae de fábrica protección contra CSRF, inyección SQL y XSS; amplia documentación y comunidad |
| MySQL 8 (InnoDB) | Almacena toda la información con llaves foráneas e índices | Base relacional madura; compatible con MySQL Workbench |
| Blade + Alpine.js + Tailwind CSS | Pantallas (Blade), interacciones ligeras (Alpine) y estilos (Tailwind) | Menos piezas que un framework de JavaScript; decisión del proyecto |
| Spatie Permission | Roles y permisos de acceso | Paquete estándar y mantenido; evita programar la autorización desde cero |
| Laravel Fortify (Fase 8) | Inicio de sesión, límite de intentos, recuperación de contraseña | Autenticación sin imponer pantallas; se usan las propias |
| PHPWord (Fase 10) | Llena los machotes de Word conservando su diseño | Única biblioteca PHP madura para plantillas .docx |
| PHPUnit | Pruebas automáticas | Incluido en Laravel |
| Git | Historial de cambios | Ramas por módulo (main, develop, feature/…) |

## Capas del sistema

| Capa | Carpeta | Qué hace |
|---|---|---|
| Migraciones | `database/migrations` | Crean y versionan las tablas |
| Seeders | `database/seeders` | Cargan los datos iniciales (catálogos, roles, administrador) |
| Modelos | `app/Models` | Representan las tablas y sus relaciones (Fase 7) |
| Políticas | `app/Policies` | Deciden quién puede ver o modificar cada registro: «solo su departamento» (Fase 8) |
| Servicios | `app/Services` | Reglas de negocio: folios, generación de documentos (Fases 9 y 10) |
| Controladores | `app/Http/Controllers` | Reciben las peticiones y devuelven respuestas |
| Vistas | `resources/views` | Pantallas en Blade |
| Plantillas Word | `resources/plantillas` | Machotes oficiales |
| Pruebas | `tests` | Comprueban que todo funciona |

## Seguridad: dónde se resuelve cada medida

| Medida | Cómo se cubre |
|---|---|
| CSRF | Middleware de Laravel en todos los formularios |
| Inyección SQL | Consultas con parámetros (Eloquent / Query Builder), nunca SQL concatenado |
| XSS | Escape automático de Blade |
| Contraseñas | Hash (bcrypt) |
| Fuerza bruta | Límite de intentos de inicio de sesión (Fortify) |
| Autorización | Roles y permisos (Spatie) + políticas por registro |
| Auditoría | Tabla `activity_log` |
| Datos sensibles | Variables de entorno en `.env`; teléfono y correo de estudiantes solo con el permiso `seguimiento.ver_contacto` |
| Archivos | Solo jpg, jpeg, png y pdf, máximo 5 MB, guardados fuera de la carpeta pública |

## Escalabilidad

El sistema es un monolito modular: un solo proyecto con módulos separados. Para el tamaño de una división (miles de registros) sobra capacidad.
Si creciera, se agregan sin rediseñar: caché, colas para generar documentos y almacenamiento externo de archivos.
Las búsquedas ya cuentan con índices en folio, fecha, matrícula, estado, servicio y responsable.

## Límites conocidos

- PHPWord reemplaza solo valores de una línea; los textos largos se dividen en varios marcadores.
- Los marcadores deben quedar en un solo bloque de texto dentro del .docx; las plantillas se preparan una por una y se prueban con documentos reales.
