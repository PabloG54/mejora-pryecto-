# Ajustes de análisis y diseño para Corte 2

Texto preparado para incorporar al documento de análisis y diseño original.
El documento original está enlazado desde el README, pero requiere iniciar
sesión en Google y no está disponible como archivo en este repositorio.

## Modelo de datos implementado

Las tablas de dominio conservan los nombres en inglés definidos en las
migraciones. Esta tabla de equivalencias permite relacionarlos con los nombres
en español del diagrama:

| Nombre en el diagrama | Tabla implementada | Modelo Laravel |
|---|---|---|
| PERIODO_ACADEMICO | `academic_periods` | `AcademicPeriod` |
| ASIGNATURA | `courses` | `Course` |
| ESTUDIANTES | `students` | `Student` |
| PROFESORES | `teachers` | `Teacher` |
| ACTIVIDAD_ESTUDIANTIL | `student_activities` | `StudentActivity` |
| SOLICITUD_ACADEMICA | `academic_requests` | `AcademicRequest` |
| MATRICULA | `tuitions` | `Tuition` |
| ALERTA_TEMPRANA | `early_warnings` | `EarlyWarning` |
| INTERVENCION_SEGUIMIENTO | `followup_interventions` | `FollowupIntervention` |
| USUARIOS | `users` | `User` |

Las tablas de soporte de Laravel son `password_reset_tokens`, `failed_jobs` y
`personal_access_tokens`. El esquema conserva 13 tablas en total: nueve
entidades de dominio, `users` y tres tablas de soporte. No se agregaron tablas.
El campo `salud_fisica_mental` continúa cifrado en el modelo `Student` y las
contraseñas de usuarios se almacenan con hash.

## Mapa de navegación implementado

| Ruta | Acceso y función |
|---|---|
| `/` | Dashboard de entrada. |
| `GET /setup` y `POST /setup` | Creación de la primera cuenta de coordinación; solo en entornos `local` o `testing` y mientras no existan usuarios. |
| `GET /login` y `POST /login` | Formulario e inicio de sesión con el guard web y sesión. |
| `/manage` | Interfaz autenticada para listar, consultar, crear, editar y eliminar los recursos que admiten esas operaciones. |
| `POST /logout` | Cierre de sesión e invalidación de la sesión. |

Las rutas de gestión usan el middleware `auth` del guard web. No se
implementaron paneles ni menús distintos para estudiantes, profesores y
coordinadores. El rol se almacena, pero actualmente no restringe las áreas de
`/manage`: cualquier usuario autenticado puede acceder a los recursos
disponibles.

## Tabla de CRUD

En las operaciones, `GET colección` lista, `GET colección/{id}` consulta,
`POST colección` crea, `PATCH/PUT colección/{id}` actualiza y
`DELETE colección/{id}` elimina. Los porcentajes marcados con asterisco son
estimaciones de cobertura de las operaciones implementadas, no una medición
de cumplimiento de todos los requisitos funcionales del proyecto. No se
asignaron códigos RF porque el documento original no estuvo disponible para
verificar su numeración.

| Entidad | Requisitos funcionales relacionados | Operaciones implementadas | % de implementación |
|---|---|---|---:|
| Periodo académico | Registrar, consultar, actualizar y eliminar periodos académicos. | Listar, consultar, crear, actualizar y eliminar. Se informa conflicto si hay matrículas, alertas, actividades o solicitudes relacionadas. | 100%* |
| Asignatura | Administrar el catálogo de asignaturas y sus créditos. | Listar, consultar, crear, actualizar y eliminar. Se informa conflicto si hay matrículas relacionadas. | 100%* |
| Estudiante | Registrar y consultar la información del estudiante y sus relaciones académicas. | Listar, consultar, crear, actualizar y eliminar si no existen registros dependientes. Validación de puntaje Saber 11 entre 0 y 500. | 100%* |
| Profesor | Registrar, consultar, actualizar y eliminar perfiles de profesores. | Listar, consultar, crear, actualizar y eliminar si no tiene intervenciones relacionadas. | 100%* |
| Actividad estudiantil | Registrar y consultar actividades de un estudiante en un periodo. | Listar, consultar, crear, actualizar y eliminar. | 100%* |
| Solicitud académica | Registrar solicitudes y actualizar su estado. | Listar, consultar, crear, actualizar y eliminar. | 100%* |
| Matrícula | Registrar y consultar matrícula, asignatura, periodo, nota y estado. | Listar, consultar, crear, actualizar y eliminar. Se valida que no se repita la combinación estudiante-asignatura-periodo. | 100%* |
| Alerta temprana | Registrar alertas de riesgo y actualizar nivel y estado. | Listar, consultar, crear, actualizar y eliminar si no tiene intervenciones relacionadas. | 100%* |
| Intervención de seguimiento | Registrar y consultar sesiones y compromisos de seguimiento. | Listar, consultar y crear. No se permite editar ni eliminar para conservar el historial inmutable. | 100%* |
| Usuario | Crear y administrar cuentas y roles de usuario, e iniciar sesión. | Listar, consultar, crear, actualizar y eliminar si no hay perfiles relacionados; inicio y cierre de sesión. Contraseñas con hash. | 100%* |

\* Estimación de cobertura de las operaciones enumeradas para cada entidad. En
intervenciones se compara con el conjunto permitido por la regla de
inmutabilidad (crear, listar y consultar). No implica que se hayan verificado
todos los requisitos funcionales originales.
