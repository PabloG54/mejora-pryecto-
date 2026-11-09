
 ## Integrante:

Samuel Galviz Cartagena — *Usuario GitHub: [@GalvizGit]
Pablo Alejandro Gaviria — *Usuario GitHub: [@PabloG54]
---

## Descripción de la Problemática y Solución

### Problemática
La deserción universitaria responde a factores multidimensionales (académicos, psicosociales y socioeconómicos) que frecuentemente se identifican de manera tardía. La dispersión de la información entre dependencias institucionales dificulta que docentes, tutores y coordinadores ejecuten un seguimiento coordinado y preventivo antes de que el estudiante abandone sus estudios.

### Solución Propuesta
"SAAT" es una plataforma web centralizada diseñada para automatizar la detección de alertas tempranas en estudiantes con riesgo de deserción, gestionar la Ficha 360° con el historial académico/psicosocial del alumno y agilizar la programación, canalización y seguimiento de intervenciones oportunas por parte del equipo docente y de bienestar.

##  Documento de Análisis y Diseño
El documento conceptual completo con los requisitos funcionales, el Diagrama Entidad-Relación normalizado, el mapa de navegación y los wireframes de la aplicación se encuentra disponible en:

(https://docs.google.com/document/d/1vwUPjhNx4w-iRkppgS7aH6lXTPbl_n0sxUM_2I4ulEU/edit?usp=sharing)

##  Requisitos del Sistema
* PHP: >= 8.2
* Composer: >= 2.x
* Node.js: >= 18.x & NPM
* Servidor Local / Base de Datos: Laragon (MySQL)

##  Instrucciones de Instalación y Ejecución

Siga estos pasos para clonar, configurar y ejecutar el proyecto en su entorno local:

### 1. Clonar el repositorio
en bash
git clone [https://github.com/GalvizGit/Web_project.git](https://github.com/GalvizGit/Web_project.git)
cd Web_project

2. Instalar dependencias del proyecto
Bash
composer install
npm install

3. Configuración del Entorno de Desarrollo
Bash
cp .env.example .env
php artisan key:generate

4. Migración de Base de Datos y Datos de Prueba
Bash
php artisan migrate --seed

5. Iniciar Servidores
Bash
php artisan serve
npm run dev

## Interfaz web y rutas CRUD

Después de iniciar sesión, visita `/manage` para probar todos los CRUD desde
la interfaz web. Permite cambiar de recurso, paginar, ver detalles, crear,
editar y eliminar registros; las relaciones se cargan en listas desplegables.
Las intervenciones de seguimiento son inmutables: solo se pueden consultar y
crear.

Las rutas CRUD están definidas en `routes/web.php` y requieren autenticación
mediante sesión con el guard `web` (`auth`). Los endpoints también se pueden probar directamente
con solicitudes JSON. Los formularios muestran requisitos y errores junto al
campo correspondiente en español. Los campos de fecha usan solo `AAAA-MM-DD`,
sin hora.

El texto de equivalencias del modelo de datos, mapa de navegación y tabla de
CRUD para incorporar al documento de análisis y diseño está en
[`docs/corte2-analisis-diseno.md`](./docs/corte2-analisis-diseno.md).

En un entorno local recién migrado, visita `/setup` para crear la primera
cuenta de coordinación. El asistente solo funciona en `local` o `testing` y
cuando aún no existen usuarios. En otras circunstancias, usa las credenciales
de una cuenta existente en `/login`.

### Datos de demostración

Ejecuta `php artisan db:seed` en local para cargar datos enlazados en todas las
áreas. El seeder es repetible, no borra otros registros y está bloqueado fuera
de `local` y `testing`.

Se crean tres cuentas por rol:

- Estudiantes: `estudiante01@saat.test`, `estudiante02@saat.test`, `estudiante03@saat.test`
- Profesores: `profesor01@saat.test`, `profesor02@saat.test`, `profesor03@saat.test`
- Coordinadores: `coordinador01@saat.test`, `coordinador02@saat.test`, `coordinador03@saat.test`

La contraseña de estas cuentas es `DemoSAAT2026!`. Tres usuarios estudiante y
tres profesores tienen perfiles enlazados; las cuentas de coordinación solo
tienen el registro de usuario. Los datos asociados incluyen periodos,
asignaturas, actividades, solicitudes, matrículas, alertas e intervenciones.

| Recurso | Endpoint |
|---|---|
| Periodos académicos | `/academic-periods` |
| Asignaturas | `/courses` |
| Estudiantes | `/students` |
| Profesores | `/teachers` |
| Actividades estudiantiles | `/student-activities` |
| Solicitudes académicas | `/academic-requests` |
| Matrículas | `/tuitions` |
| Alertas tempranas | `/early-warnings` |
| Intervenciones de seguimiento | `/followup-interventions` |
| Usuarios | `/users` |

Salvo las intervenciones, cada recurso ofrece `GET` para listar, `POST` para
crear, `GET /{id}` para consultar, `PUT/PATCH /{id}` para actualizar y
`DELETE /{id}` para eliminar. Las intervenciones solo permiten listar, crear y
consultar: el historial es inmutable (RNF04). Los listados se paginan de 15 en
15. La validación devuelve `422`; una eliminación bloqueada por registros
relacionados devuelve `409`. Las contraseñas se almacenan mediante hash y los
datos de salud mantienen el cifrado configurado en el modelo.

Ramas de Trabajo
Para mantener un flujo de desarrollo ordenado, utilizaremos las siguientes ramas:

main: Rama principal de producción. Contiene el código estable y listo para producción. No se commitea directamente aquí.

dev: Rama de integración y desarrollo. Aquí se unifican las nuevas características antes de pasar a producción.

feature/*: Ramas temporales para desarrollar nuevas funcionalidades o corregir errores específicos (ej. feature/login, feature/carrito). Se crean a partir de dev y se fusionan mediante Pull Request.

Convenciones de Commits (Conventional Commits)
Formato estandarizado: <tipo>(<módulo>): <descripción>

feat: Desarrollo de una nueva funcionalidad.

fix: Corrección de errores.

style: Cambios visuales, CSS o plantilla Blade.

docs: Documentación o cambios en el README.

chore: Configuraciones iniciales o dependencias.
