# Decisiones de diseño

| # | Decisión | Motivo |
|---|---|---|
| D1 | Laravel 13 con PHP 8.3 | Versión vigente; si el hosting solo ofrece PHP 8.2 se usaría Laravel 12 |
| D2 | Un solo catálogo `personal` con cargos múltiples (`personal_rol`) | Una persona puede firmar, recibir documentos y ser responsable sin duplicarse |
| D3 | `documento_firmante` guarda copia del nombre y puesto | Si cambia el personal, los documentos ya emitidos no se alteran |
| D4 | Folio con contador por tipo de documento y año, con bloqueo de fila | Evita folios duplicados aunque dos personas generen a la vez |
| D5 | Un trámite por tabla (residencia, servicio social, actividad complementaria) | Cada trámite tiene campos distintos; así se valida mejor |
| D6 | Seguimiento de estudiantes sin tabla nueva | La ubicación sale del trámite en curso, no se captura dos veces |
| D7 | Usuario de Consulta ve la ubicación de los estudiantes pero no su teléfono ni correo | Minimiza el acceso a datos personales |
| D8 | El departamento de una atención se obtiene del servicio | Evita que servicio y departamento se contradigan |
| D9 | Se conserva el .docx generado y sus datos, con hash SHA-256 | Son documentos oficiales; permite comprobar que no se alteraron |
| D10 | Spatie Permission `^6.25` | Versión compatible con Laravel 13 |
| D11 | Zona horaria `America/Mexico_City` | Las fechas y horas de la bitácora deben corresponder a la hora local |
| D12 | Tabla `activity_log` con estructura compatible con Spatie Activitylog | En la Fase 13 se verifica el paquete con Laravel 13; si no, se usa un registro propio sobre la misma tabla |
| D13 | `users.personal_id` único y opcional | Un usuario que también firma o atiende es el mismo registro de personal |
| D14 | La contraseña del administrador inicial viene de `.env` | Ninguna contraseña queda escrita en el código |
| D15 | La bitácora de entradas y salidas del Área de Sistemas es la misma tabla `atenciones`: toda entrada es una atención con hora de entrada (`fecha_hora`), hora de salida (`hora_salida`) y tipo de solicitante | Evita llevar dos registros de lo mismo; permite contar por día cuántos alumnos y docentes entran y cuántos se atienden |
