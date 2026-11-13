# Módulo de Documentos — Análisis detallado de los 5 machotes

## Observaciones antes de las tablas (no corregí nada, solo señalo)

1. **La Carta de Aceptación de Residencia es la única de las 4 cartas que NO trae el párrafo institucional** (Decreto del Ejecutivo, Gaceta N° 51, ISO 9001/14001) que sí aparece en Término de Residencia, Aceptación de SS y Término de SS. Puede ser intencional o un descuido del machote — pregunto en la sección de pendientes.
2. **Texto duplicado en el destinatario de las 4 cartas**: `JEFA DEL DEPARTAMENTO DE SERVICIO SOCIAL Y / Y RESIDENCIAS PROFESIONALES.` — la "Y" está repetida en dos líneas. Lo dejo tal cual hasta que confirmes si se corrige.
3. **Fechas de oficio incompletas** en Término de SS (`a de agosto de`) y Aceptación de SS (`a de mayo de`) — el machote no trae día ni año de ejemplo, a diferencia de las otras 3 donde sí hay fecha completa. Confirma que esto es solo un campo variable sin llenar y no una plantilla rota.
4. **Dos personas distintas con rol de "Subdirección" en la Constancia**: el **destinatario** es Eric Ernesto Soriano Mendieta (Subdirector de Servicios Escolares), y el **firmante Vo.Bo.** es Yareli Hernández Rodriguez (Encargada del Despacho de la Subdirección de Estudios Profesionales). Son roles distintos — no deben tratarse como el mismo firmante.
5. **Los destinatarios de las 4 cartas (Ing. Carolina Anabel Ayala Hernández) y de la Constancia (Eric Ernesto Soriano Mendieta) hoy están escritos como texto fijo**, no como firmantes seleccionables. Técnicamente son personal que también puede rotar — lo marco como pendiente de decisión en la sección 12.
6. **Inconsistencia resuelta**: el segundo archivo de ejemplos (`PARA_EL_ESTUDIANTE_axel.docx`) mostraba la Carta de Término de Residencia Profesional sin el párrafo institucional, distinto al machote original. **Confirmado: la versión vigente es la del machote original, CON el párrafo institucional** (Decreto/ISO).

---

## 1. Carta de Aceptación de Residencia Profesional

| Campo | Tipo | Fijo/Variable | Obligatorio | Origen |
|---|---|---|---|---|
| Fecha de oficio | Fecha | Variable | Sí | Trámite |
| Destinatario (nombre y puesto) | Texto | Fijo (hoy) | Sí | Configuración |
| Nombre del estudiante | Texto | Variable | Sí | Estudiante |
| Matrícula | Texto | Variable | Sí | Estudiante |
| Carrera | Texto | Fijo | Sí | Configuración |
| Proyecto de residencia | Texto | Variable | Sí | Trámite (Residencia) |
| Fecha de inicio | Fecha | Variable | Sí | Trámite |
| Fecha de término | Fecha | Variable | Sí | Trámite |
| Horas totales | Número | Variable | Sí | Trámite |
| Firmante (Jefe de División) | Relación | Variable | Sí | Catálogo Firmantes |
| Folio | Texto | Autogenerado | Sí | Sistema |

## 2. Carta de Término de Residencia Profesional

Mismos campos que la de Aceptación, más:

| Campo | Tipo | Fijo/Variable | Obligatorio | Origen |
|---|---|---|---|---|
| Párrafo institucional (Decreto/ISO 9001-14001) | Texto | Fijo | Sí | Plantilla |
| *(resto de campos)* | — | — | — | igual que tabla 1 |

## 3. Carta de Término de Servicio Social

| Campo | Tipo | Fijo/Variable | Obligatorio | Origen |
|---|---|---|---|---|
| Fecha de oficio | Fecha | Variable | Sí | Trámite |
| Destinatario (nombre y puesto) | Texto | Fijo (hoy) | Sí | Configuración |
| Párrafo institucional (Decreto/ISO) | Texto | Fijo | Sí | Plantilla |
| Nombre del estudiante | Texto | Variable | Sí | Estudiante |
| Matrícula | Texto | Variable | Sí | Estudiante |
| Carrera | Texto | Fijo | Sí | Configuración |
| Fecha de inicio | Fecha | Variable | Sí | Trámite |
| Fecha de término | Fecha | Variable | Sí | Trámite |
| Horas totales | Número | Variable | Sí | Trámite |
| Firmante (Jefe de División) | Relación | Variable | Sí | Catálogo Firmantes |
| Folio | Texto | Autogenerado | Sí | Sistema |

*Nota: el machote no imprime institución receptora ni nombre del programa de Servicio Social — solo fechas y horas. Si quieres llevar ese dato internamente aunque no salga en la carta, lo agrego al trámite (ver sección 12).*

## 4. Carta de Aceptación de Servicio Social

Misma estructura que la tabla 3 (campos idénticos).

## 5. Constancia de Acreditación de Actividades Complementarias

| Campo | Tipo | Fijo/Variable | Obligatorio | Origen |
|---|---|---|---|---|
| Folio | Texto | Autogenerado | Sí | Sistema |
| Destinatario (nombre y puesto) | Texto | Fijo (hoy) | Sí | Configuración |
| Nombre del estudiante | Texto | Variable | Sí | Estudiante |
| Número de control/matrícula | Texto | Variable | Sí | Estudiante |
| Carrera | Texto | Fijo | Sí | Configuración |
| Tipo de actividad (ej. "participación en el CURSO MOOC") | Texto | Variable | Sí | Trámite (Actividad Complementaria) |
| Nombre de la actividad/curso | Texto | Variable | Sí | Trámite |
| Periodo escolar (ej. 2025-2026/2) | Texto | Variable | Sí | Trámite |
| Créditos otorgados | Número | Variable | Sí | Trámite |
| Firmante 1 — Jefe de División (Atentamente) | Relación | Variable | Sí | Catálogo Firmantes |
| Firmante 2 — Docente (Vo.Bo.) | Relación | Variable | Sí | Catálogo Firmantes |
| Firmante 3 — Subdirección académica (Vo.Bo., bloque aparte) | Relación | Variable | Sí | Catálogo Firmantes |

---

## Resultado consolidado

**1-3. Campos, fijos y variables** → ver tablas arriba. Fijos en todo el sistema: carrera, textos institucionales, diseño/tipografía/membrete/pie de página. Variables: identidad del estudiante, fechas, periodo, horas, créditos, proyecto/actividad, firmantes, folio.

**4. Firmantes necesarios en el catálogo:**
- Mtro. Ebner Juárez Elías — Jefe de División (aparece en las 5)
- Mtra. María del Carmen Magaña González — Docente (solo Constancia, cambia según la actividad/curso)
- Mtra. Yareli Hernández Rodriguez — Subdirección académica (solo Constancia)
- *(a decidir, ver punto 12)* Ing. Carolina Anabel Ayala Hernández — destinataria de las 4 cartas
- *(a decidir)* Mtro. Eric Ernesto Soriano Mendieta — destinatario de la Constancia

**5. Folios propuestos** — técnicamente adecuados; el patrón `PREFIJO-AÑO-00000` con consecutivo independiente por tipo y año, bajo transacción con bloqueo de fila, evita duplicados incluso con captura simultánea. No veo problema técnico con el formato que ya definimos.

**6. Relaciones necesarias:**
`Estudiante 1:N Trámite` (Residencia / Servicio Social / Actividad Complementaria) → `Trámite 1:N DocumentoGenerado` (puede regenerarse más de una vez) → `DocumentoGenerado N:M Firmante` (vía pivote, con rol de firma) → `DocumentoGenerado 1:1 TipoDocumento` → `DocumentoGenerado 1:1 Folio`.

**7. Tablas probables:**
`estudiantes`, `tramites_residencia`, `tramites_servicio_social`, `tramites_actividad_complementaria` (tablas separadas por trámite, no una genérica — cada una tiene campos distintos y así se valida mejor cada una), `tipos_documento`, `documentos_generados`, `firmantes`, `documento_firmante`, `folios_contador`, y opcionalmente `destinatarios` si decides moverlos a catálogo (punto 12).

**8. Pertenece al estudiante:** nombre, matrícula, carrera.

**9. Pertenece al trámite:** fechas de inicio/término, horas, proyecto (Residencia), periodo escolar, tipo de actividad/curso y créditos (Actividades Complementarias).

**10. Pertenece al documento generado:** folio, tipo de documento, fecha de generación, usuario que lo generó, firmantes seleccionados para esa generación, referencia al trámite/estudiante, nombre del archivo.

**11. Pertenece al catálogo de firmantes:** nombre, puesto, tipo de firma, estado activo/inactivo.

**12. Información que falta definir:**
- ¿Los destinatarios (Carolina Ayala, Eric Soriano) también deben manejarse desde el catálogo de firmantes/personal, o quedan fijos como texto institucional?
- ¿Se agrega el párrafo institucional a la Carta de Aceptación de Residencia para que las 4 cartas sean consistentes, o se deja tal como está el machote actual?
- ¿Se corrige la "Y" duplicada en el puesto de la destinataria, o se conserva igual que el machote original?
- ¿Vale la pena guardar institución/programa del Servicio Social como dato interno del trámite aunque no se imprima en la carta?

---

## Tecnología para generar el Word

Confirmo lo ya propuesto: **PHPWord con `TemplateProcessor`**. Es la única vía madura en PHP para tomar un .docx existente y sustituir marcadores de texto sin reconstruir el documento — conserva encabezado, pie de página, tablas, tipografía y saltos de página tal como están en el machote, siempre que los marcadores no queden partidos entre "runs" de formato distinto (esto se verifica al preparar cada plantilla, documento por documento).

Flujo (1 a 8 de tu punto 11): tipo de documento → estudiante (buscar o capturar) → datos del trámite → firmantes (del catálogo) → folio (autogenerado, atómico) → generación del .docx → registro en `documentos_generados` → descarga.

## Conservación de documentos generados

| Opción | Ventaja | Desventaja |
|---|---|---|
| A. Solo datos, regenerar bajo demanda | Ahorra espacio | Si la plantilla institucional cambia después, el documento regenerado ya no coincide con lo que se imprimió y firmó originalmente |
| B. Guardar solo el archivo .docx | Conserva exactamente lo entregado/firmado | No queda estructurado para búsquedas/reportes; más difícil de auditar por campos |
| C. Guardar ambos (datos + archivo) | Fiel a lo entregado y consultable/filtrable desde el sistema | Mayor uso de almacenamiento |

**Recomendación: opción C.** Son documentos oficiales que se imprimen y firman a mano — conviene tener el snapshot exacto de lo que se entregó, y el volumen de este tipo de documentos (comparado con la bitácora general) es bajo, así que el espacio no debería ser un problema real.
