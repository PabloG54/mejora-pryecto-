
 ## Integrante:

Samuel Galviz Cartagena — *Usuario GitHub: [@GalvizGit]
Pablo Alejandro Gaviria — *Usuario GitHub: [@NAnay576]
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






