<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\AcademicRequest;
use App\Models\Course;
use App\Models\EarlyWarning;
use App\Models\FollowupIntervention;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\Teacher;
use App\Models\Tuition;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrudApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_endpoints_require_authentication(): void
    {
        $this->getJson('/students')->assertUnauthorized();
    }

    public function test_management_routes_use_the_web_auth_middleware(): void
    {
        $middleware = app('router')->getRoutes()->getByName('crud.index')->gatherMiddleware();

        $this->assertContains('auth', $middleware);
        $this->assertNotContains('auth:sanctum', $middleware);
    }

    public function test_saber11_score_is_limited_to_500_on_create_and_update(): void
    {
        $this->authenticate();

        $userId = $this->postJson('/users', [
            'correo_inst' => 'saber11@example.edu',
            'contrasena' => 'secure-pass-123',
            'rol' => 'estudiante',
        ])->assertCreated()->json('id_usuario');

        $studentId = $this->postJson('/students', [
            'id_usuario' => $userId,
            'codigo_estud' => 'EST-SABER11',
            'doc_identidad' => 'DOC-SABER11',
            'nombres' => 'Estudiante',
            'apellidos' => 'Prueba',
            'puntaje_saber11' => 500,
        ])->assertCreated()->json('id_estudiante');

        $this->patchJson("/students/{$studentId}", ['puntaje_saber11' => 501])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['puntaje_saber11']);
    }

    public function test_course_can_be_created_read_updated_and_deleted(): void
    {
        $this->authenticate();

        $this->postJson('/courses', [
            'codigo_asig' => 'MAT101',
            'nombre_asig' => 'Matemáticas',
            'programa_academico' => 'Ingeniería',
            'creditos' => 3,
        ])->assertCreated()
            ->assertJsonPath('codigo_asig', 'MAT101')
            ->assertJsonMissingPath('contrasena');

        $courseId = Course::query()->value('id_asignatura');

        $this->getJson("/courses/{$courseId}")
            ->assertOk()
            ->assertJsonPath('nombre_asig', 'Matemáticas');

        $this->patchJson("/courses/{$courseId}", ['creditos' => 4])
            ->assertOk()
            ->assertJsonPath('creditos', 4);

        $this->deleteJson("/courses/{$courseId}")->assertNoContent();
    }

    public function test_required_field_errors_are_returned_in_spanish(): void
    {
        $this->authenticate();

        $this->postJson('/courses', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['codigo_asig', 'nombre_asig', 'programa_academico', 'creditos'])
            ->assertJsonPath('errors.codigo_asig.0', 'El campo código de asignatura es obligatorio.');
    }

    public function test_academic_period_dates_only_accept_a_date_without_time(): void
    {
        $this->authenticate();

        $this->postJson('/academic-periods', [
            'nombre_periodo' => '2026-2-time-test',
            'fecha_inicio' => '2026-07-13T08:30:00',
            'fecha_fin' => '2026-11-27',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['fecha_inicio'])
            ->assertJsonPath('errors.fecha_inicio.0', 'El campo fecha de inicio debe usar el formato Y-m-d (solo fecha, sin hora).');
    }

    public function test_user_password_is_hashed_and_related_records_are_created(): void
    {
        $this->authenticate();

        $userResponse = $this->postJson('/users', [
            'correo_inst' => 'student@example.edu',
            'contrasena' => 'secure-pass-123',
            'rol' => 'estudiante',
        ])->assertCreated()
            ->assertJsonMissingPath('contrasena');

        $studentUserId = $userResponse->json('id_usuario');
        $this->assertTrue(Hash::check('secure-pass-123', User::findOrFail($studentUserId)->contrasena));

        $periodId = $this->postJson('/academic-periods', [
            'nombre_periodo' => '2026-1',
            'fecha_inicio' => '2026-01-15',
            'fecha_fin' => '2026-06-15',
        ])->assertCreated()->json('id_periodo');

        $courseId = $this->postJson('/courses', [
            'codigo_asig' => 'BIO101',
            'nombre_asig' => 'Biología',
            'programa_academico' => 'Ciencias',
            'creditos' => 3,
        ])->assertCreated()->json('id_asignatura');

        $studentId = $this->postJson('/students', [
            'id_usuario' => $studentUserId,
            'codigo_estud' => 'EST-001',
            'doc_identidad' => 'DOC-001',
            'nombres' => 'Ana',
            'apellidos' => 'Pérez',
            'salud_fisica_mental' => 'Dato confidencial',
        ])->assertCreated()
            ->assertJsonPath('salud_fisica_mental', 'Dato confidencial')
            ->json('id_estudiante');

        $teacherUserId = $this->postJson('/users', [
            'correo_inst' => 'teacher@example.edu',
            'contrasena' => 'secure-pass-456',
            'rol' => 'profesor',
        ])->assertCreated()->json('id_usuario');

        $teacherId = $this->postJson('/teachers', [
            'id_usuario' => $teacherUserId,
            'departamento' => 'Ciencias',
            'facultad' => 'Ingeniería',
        ])->assertCreated()->json('id_profesor');

        $activityId = $this->postJson('/student-activities', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_actividad' => 'investigacion',
            'nombre_actividad' => 'Proyecto de laboratorio',
        ])->assertCreated()->json('id_actividad');

        $requestId = $this->postJson('/academic-requests', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_solicitud' => 'excusa',
            'fecha_radicacion' => '2026-02-01',
        ])->assertCreated()->json('id_solicitud');

        $tuitionId = $this->postJson('/tuitions', [
            'id_estudiante' => $studentId,
            'id_asignatura' => $courseId,
            'id_periodo' => $periodId,
            'nota_final' => 4.5,
        ])->assertCreated()->json('id_matricula');

        $this->postJson('/tuitions', [
            'id_estudiante' => $studentId,
            'id_asignatura' => $courseId,
            'id_periodo' => $periodId,
        ])->assertUnprocessable();

        $warningId = $this->postJson('/early-warnings', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_riesgo' => 'academico',
            'nivel_riesgo' => 'medio',
            'fecha_reporte' => '2026-03-01',
        ])->assertCreated()->json('id_alerta');

        $interventionId = $this->postJson('/followup-interventions', [
            'id_alerta' => $warningId,
            'id_profesor' => $teacherId,
            'fecha_sesion' => '2026-03-05',
            'tipo_intervencion' => 'tutoria',
            'observaciones' => 'Revisión de avance académico',
        ])->assertCreated()->json('id_intervencion');

        $this->getJson("/student-activities/{$activityId}")->assertOk();
        $this->getJson("/academic-requests/{$requestId}")->assertOk();
        $this->getJson("/tuitions/{$tuitionId}")->assertOk();
        $this->getJson("/early-warnings/{$warningId}")->assertOk();
        $this->getJson('/followup-interventions')->assertOk()->assertJsonFragment(['id_intervencion' => $interventionId]);
        $this->getJson("/followup-interventions/{$interventionId}")->assertOk();
        $this->putJson("/followup-interventions/{$interventionId}", [])->assertMethodNotAllowed();
        $this->deleteJson("/followup-interventions/{$interventionId}")->assertMethodNotAllowed();
    }

    public function test_each_mutable_entity_supports_create_list_show_update_and_delete(): void
    {
        $this->authenticate();

        $periodId = $this->postJson('/academic-periods', [
            'nombre_periodo' => 'CRUD-PERIOD',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-06-30',
        ])->assertCreated()->json('id_periodo');
        $this->getJson('/academic-periods')->assertOk()->assertJsonFragment(['id_periodo' => $periodId]);
        $this->getJson("/academic-periods/{$periodId}")->assertOk();
        $this->patchJson("/academic-periods/{$periodId}", ['nombre_periodo' => 'CRUD-PERIOD-UPDATED'])
            ->assertOk()->assertJsonPath('nombre_periodo', 'CRUD-PERIOD-UPDATED');

        $courseId = $this->postJson('/courses', [
            'codigo_asig' => 'CRUD-COURSE',
            'nombre_asig' => 'Asignatura CRUD',
            'programa_academico' => 'Pruebas',
            'creditos' => 3,
        ])->assertCreated()->json('id_asignatura');
        $this->getJson('/courses')->assertOk()->assertJsonFragment(['id_asignatura' => $courseId]);
        $this->getJson("/courses/{$courseId}")->assertOk();
        $this->patchJson("/courses/{$courseId}", ['nombre_asig' => 'Asignatura CRUD actualizada'])
            ->assertOk()->assertJsonPath('nombre_asig', 'Asignatura CRUD actualizada');

        $studentUserId = $this->postJson('/users', [
            'correo_inst' => 'crud-student@example.edu',
            'contrasena' => 'secure-pass-123',
            'rol' => 'estudiante',
        ])->assertCreated()->json('id_usuario');
        $studentId = $this->postJson('/students', [
            'id_usuario' => $studentUserId,
            'codigo_estud' => 'CRUD-STUDENT',
            'doc_identidad' => 'CRUD-DOC-STUDENT',
            'nombres' => 'Estudiante CRUD',
            'apellidos' => 'Prueba',
            'puntaje_saber11' => 500,
        ])->assertCreated()->json('id_estudiante');
        $this->getJson('/students')->assertOk()->assertJsonFragment(['id_estudiante' => $studentId]);
        $this->getJson("/students/{$studentId}")->assertOk();
        $this->patchJson("/students/{$studentId}", ['nombres' => 'Estudiante CRUD actualizado'])
            ->assertOk()->assertJsonPath('nombres', 'Estudiante CRUD actualizado');

        $teacherUserId = $this->postJson('/users', [
            'correo_inst' => 'crud-teacher@example.edu',
            'contrasena' => 'secure-pass-456',
            'rol' => 'profesor',
        ])->assertCreated()->json('id_usuario');
        $teacherId = $this->postJson('/teachers', [
            'id_usuario' => $teacherUserId,
            'departamento' => 'Pruebas',
            'facultad' => 'Pruebas',
        ])->assertCreated()->json('id_profesor');
        $this->getJson('/teachers')->assertOk()->assertJsonFragment(['id_profesor' => $teacherId]);
        $this->getJson("/teachers/{$teacherId}")->assertOk();
        $this->patchJson("/teachers/{$teacherId}", ['facultad' => 'Facultad actualizada'])
            ->assertOk()->assertJsonPath('facultad', 'Facultad actualizada');

        $activityId = $this->postJson('/student-activities', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_actividad' => 'investigacion',
            'nombre_actividad' => 'Actividad CRUD',
        ])->assertCreated()->json('id_actividad');
        $this->getJson('/student-activities')->assertOk()->assertJsonFragment(['id_actividad' => $activityId]);
        $this->getJson("/student-activities/{$activityId}")->assertOk();
        $this->patchJson("/student-activities/{$activityId}", ['nombre_actividad' => 'Actividad CRUD actualizada'])
            ->assertOk()->assertJsonPath('nombre_actividad', 'Actividad CRUD actualizada');

        $requestId = $this->postJson('/academic-requests', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_solicitud' => 'excusa',
            'fecha_radicacion' => '2026-02-01',
        ])->assertCreated()->json('id_solicitud');
        $this->getJson('/academic-requests')->assertOk()->assertJsonFragment(['id_solicitud' => $requestId]);
        $this->getJson("/academic-requests/{$requestId}")->assertOk();
        $this->patchJson("/academic-requests/{$requestId}", ['estado' => 'en_tramite'])
            ->assertOk()->assertJsonPath('estado', 'en_tramite');

        $tuitionId = $this->postJson('/tuitions', [
            'id_estudiante' => $studentId,
            'id_asignatura' => $courseId,
            'id_periodo' => $periodId,
            'nota_final' => 4.0,
        ])->assertCreated()->json('id_matricula');
        $this->getJson('/tuitions')->assertOk()->assertJsonFragment(['id_matricula' => $tuitionId]);
        $this->getJson("/tuitions/{$tuitionId}")->assertOk();
        $this->patchJson("/tuitions/{$tuitionId}", ['nota_final' => 4.5])
            ->assertOk()->assertJsonPath('nota_final', '4.5');
        $this->postJson('/tuitions', [
            'id_estudiante' => $studentId,
            'id_asignatura' => $courseId,
            'id_periodo' => $periodId,
        ])->assertUnprocessable()
            ->assertJsonPath('errors.id_estudiante.0', 'El estudiante ya tiene esta asignatura matriculada en el periodo seleccionado.');

        $warningId = $this->postJson('/early-warnings', [
            'id_estudiante' => $studentId,
            'id_periodo' => $periodId,
            'tipo_riesgo' => 'academico',
            'nivel_riesgo' => 'medio',
            'fecha_reporte' => '2026-03-01',
        ])->assertCreated()->json('id_alerta');
        $this->getJson('/early-warnings')->assertOk()->assertJsonFragment(['id_alerta' => $warningId]);
        $this->getJson("/early-warnings/{$warningId}")->assertOk();
        $this->patchJson("/early-warnings/{$warningId}", ['nivel_riesgo' => 'alto'])
            ->assertOk()->assertJsonPath('nivel_riesgo', 'alto');

        $userId = $this->postJson('/users', [
            'correo_inst' => 'crud-user@example.edu',
            'contrasena' => 'secure-pass-789',
            'rol' => 'coordinador',
        ])->assertCreated()->json('id_usuario');
        $this->getJson('/users')->assertOk()->assertJsonFragment(['id_usuario' => $userId]);
        $this->getJson("/users/{$userId}")->assertOk();
        $this->patchJson("/users/{$userId}", ['estado' => 'inactivo'])
            ->assertOk()->assertJsonPath('estado', 'inactivo');

        $this->deleteJson("/users/{$userId}")->assertNoContent();
        $this->deleteJson("/early-warnings/{$warningId}")->assertNoContent();
        $this->deleteJson("/tuitions/{$tuitionId}")->assertNoContent();
        $this->deleteJson("/academic-requests/{$requestId}")->assertNoContent();
        $this->deleteJson("/student-activities/{$activityId}")->assertNoContent();
        $this->deleteJson("/students/{$studentId}")->assertNoContent();
        $this->deleteJson("/teachers/{$teacherId}")->assertNoContent();
        $this->deleteJson("/users/{$studentUserId}")->assertNoContent();
        $this->deleteJson("/users/{$teacherUserId}")->assertNoContent();
        $this->deleteJson("/courses/{$courseId}")->assertNoContent();
        $this->deleteJson("/academic-periods/{$periodId}")->assertNoContent();
    }

    public function test_initial_setup_logs_in_and_opens_the_crud_views(): void
    {
        $this->get('/setup')->assertOk()->assertSee('Configurar SAAT');

        $this->post('/setup', [
            'correo_inst' => 'admin@example.edu',
            'password' => 'secure-admin-pass',
            'password_confirmation' => 'secure-admin-pass',
        ])->assertRedirect(route('crud.index'));

        $this->get('/manage')
            ->assertOk()
            ->assertSee('Gestión de datos')
            ->assertSee('followup-interventions')
            ->assertSee('Guardar cambios');

        $this->post('/logout')->assertRedirect(route('login'));
        $this->get('/manage')->assertRedirect(route('login'));
    }

    public function test_manage_view_defines_a_list_and_form_for_every_resource(): void
    {
        $this->authenticate();

        $response = $this->get('/manage')->assertOk();
        foreach ([
            'academic-periods',
            'courses',
            'students',
            'teachers',
            'student-activities',
            'academic-requests',
            'tuitions',
            'early-warnings',
            'followup-interventions',
            'users',
        ] as $resource) {
            $response->assertSee($resource);
        }

        $response->assertSee('readOnly: true')
            ->assertSee('Editar')
            ->assertSee('Eliminar')
            ->assertSee('Nuevo registro');
    }

    public function test_demo_data_seeder_populates_all_resources_idempotently(): void
    {
        Artisan::call('db:seed', ['--class' => DemoDataSeeder::class]);
        Artisan::call('db:seed', ['--class' => DemoDataSeeder::class]);

        $this->assertSame(9, User::where('correo_inst', 'like', '%@saat.test')->count());
        $this->assertSame(3, Student::where('codigo_estud', 'like', 'DEMO-EST-%')->count());
        $this->assertSame(3, Teacher::whereHas('usuario', fn ($query) => $query->where('correo_inst', 'like', 'profesor%@saat.test'))->count());
        $this->assertSame(3, AcademicPeriod::where('nombre_periodo', 'like', 'DEMO-%')->count());
        $this->assertSame(3, Course::where('codigo_asig', 'like', 'DEMO%')->count());
        $this->assertSame(3, StudentActivity::where('nombre_actividad', 'like', 'Actividad de prueba %')->count());
        $this->assertSame(3, AcademicRequest::whereHas('estudiante', fn ($query) => $query->where('codigo_estud', 'like', 'DEMO-EST-%'))->count());
        $this->assertSame(3, Tuition::whereHas('estudiante', fn ($query) => $query->where('codigo_estud', 'like', 'DEMO-EST-%'))->count());
        $this->assertSame(3, EarlyWarning::whereHas('estudiante', fn ($query) => $query->where('codigo_estud', 'like', 'DEMO-EST-%'))->count());
        $this->assertSame(3, FollowupIntervention::whereHas('alerta.estudiante', fn ($query) => $query->where('codigo_estud', 'like', 'DEMO-EST-%'))->count());

        $demoUser = User::where('correo_inst', 'estudiante01@saat.test')->firstOrFail();
        $this->assertTrue(Hash::check('DemoSAAT2026!', $demoUser->contrasena));
    }

    private function authenticate(): void
    {
        $user = User::create([
            'correo_inst' => 'coordinator@example.edu',
            'contrasena' => Hash::make('secure-admin-pass'),
            'rol' => 'coordinador',
            'estado' => 'activo',
        ]);

        $this->actingAs($user, 'web');
    }
}
