# Sistema de Gestión y Bitácora — División de Ingeniería en Sistemas Computacionales (TESI)

Sistema web para registrar las atenciones del Área de Sistemas, generar automáticamente los documentos oficiales
(cartas de Servicio Social y Residencia Profesional y constancia de actividades complementarias) y dar seguimiento
a los estudiantes. Proyecto de Residencia Profesional.

**Estado actual: Fase 6 — estructura de base de datos creada** (25 migraciones, datos iniciales y prueba de estructura).
Las pantallas, el inicio de sesión y los módulos se construyen en las fases siguientes (ver `docs/04_trazabilidad_requisitos.md`).

## Qué necesitas instalar

| Programa | Versión | Para qué |
|---|---|---|
| PHP | 8.3 o superior, con extensiones `mbstring`, `xml`, `zip`, `pdo_mysql`, `pdo_sqlite` | Ejecuta Laravel |
| Composer | 2.x | Instala Laravel y sus paquetes |
| MySQL Server | 8.x | Guarda la información |
| MySQL Workbench | 8.x | Ventana para ver y consultar la base de datos |
| Node.js | LTS | Compila el diseño de las pantallas (se usa desde la Fase 9) |
| Git | cualquiera | Control de cambios |

MySQL Workbench **no es la base de datos**: es solo la herramienta para verla. La base la guarda MySQL Server.

## Puesta en marcha

1. Descomprime el proyecto y abre una terminal en la carpeta.
2. Instala los paquetes: `composer install`
3. Crea tu archivo de entorno: copia `.env.example` como `.env` y ejecuta `php artisan key:generate`
4. En MySQL Workbench ejecuta:
   `CREATE DATABASE sistema_bitacora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
5. Edita `.env`: `DB_USERNAME`, `DB_PASSWORD`, y define `ADMIN_EMAIL` y `ADMIN_PASSWORD` (mínimo 12 caracteres).
6. Publica las tablas de roles y permisos del paquete Spatie:
   `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
7. Crea las tablas y los datos iniciales: `php artisan migrate --seed`
8. Comprueba: `php artisan migrate:status` (todas en «Ran») y `php artisan test` (las pruebas deben pasar).
9. En Workbench, actualiza el esquema `sistema_bitacora`: deben aparecer las tablas.
   Para ver el diagrama: *Database → Reverse Engineer*.

Si algo falla, copia el mensaje de error completo; con eso se corrige.

## Documentación (`docs/`)

| Archivo | Contenido |
|---|---|
| `01_arquitectura_y_tecnologias.md` | Qué tecnología se usa, para qué sirve y por qué |
| `02_diccionario_de_datos.md` | Cada tabla y campo: qué guarda y para qué sirve |
| `03_decisiones_de_diseno.md` | Decisiones tomadas y su motivo |
| `04_trazabilidad_requisitos.md` | De cada requisito a su módulo, tablas y estado |

`database/sql/sistema_bitacora.sql` es el mismo diseño en SQL, para revisarlo en MySQL Workbench sin ejecutar Laravel.
