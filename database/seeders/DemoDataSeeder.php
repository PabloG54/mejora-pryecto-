<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    private const PASSWORD = 'DemoSAAT2026!';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Los datos de demostración solo se pueden cargar en local o testing.');
        }

        DB::transaction(function (): void {
            $users = [];

            foreach (['estudiante', 'profesor', 'coordinador'] as $role) {
                for ($number = 1; $number <= 3; $number++) {
                    $email = sprintf('%s%02d@saat.test', $role, $number);
                    $users[$role][$number] = User::updateOrCreate(
                        ['correo_inst' => $email],
                        [
                            'contrasena' => Hash::make(self::PASSWORD),
                            'rol' => $role,
                            'estado' => 'activo',
                        ]
                    );
                }
            }

            $students = [];
            $studentDetails = [
                ['Ana', 'Gómez', 'Colegio Central', 365, 82],
                ['Luis', 'Pérez', 'Instituto del Norte', 342, 78],
                ['Sofía', 'Ramírez', 'Liceo Moderno', 389, 91],
            ];

            foreach ($studentDetails as $index => [$firstName, $lastName, $school, $examScore, $admissionScore]) {
                $number = $index + 1;
                $students[$number] = Student::updateOrCreate(
                    ['codigo_estud' => sprintf('DEMO-EST-%03d', $number)],
                    [
                        'id_usuario' => $users['estudiante'][$number]->id_usuario,
                        'doc_identidad' => sprintf('DEMO-DOC-%03d', $number),
                        'nombres' => $firstName,
                        'apellidos' => $lastName,
                        'colegio_origen' => $school,
                        'puntaje_saber11' => $examScore,
                        'prueba_admision' => $admissionScore,
                        'salud_fisica_mental' => 'Registro de prueba: sin observaciones.',
                    ]
                );
            }

            $teachers = [];
            $teacherDetails = [
                ['Ciencias Básicas', 'Ingeniería'],
                ['Bienestar Universitario', 'Ciencias Sociales'],
                ['Matemáticas', 'Ciencias Exactas'],
            ];

            foreach ($teacherDetails as $index => [$department, $faculty]) {
                $number = $index + 1;
                $teachers[$number] = Teacher::updateOrCreate(
                    ['id_usuario' => $users['profesor'][$number]->id_usuario],
                    ['departamento' => $department, 'facultad' => $faculty]
                );
            }

            $periodDetails = [
                ['DEMO-2025-2', '2025-07-14', '2025-11-28'],
                ['DEMO-2026-1', '2026-01-19', '2026-06-05'],
                ['DEMO-2026-2', '2026-07-13', '2026-11-27'],
            ];
            $periods = [];

            foreach ($periodDetails as [$name, $startDate, $endDate]) {
                $periods[] = AcademicPeriod::updateOrCreate(
                    ['nombre_periodo' => $name],
                    ['fecha_inicio' => $startDate, 'fecha_fin' => $endDate]
                );
            }

            $courseDetails = [
                ['DEMO101', 'Fundamentos de Matemáticas', 'Ingeniería', 4],
                ['DEMO102', 'Introducción a la Programación', 'Ingeniería', 3],
                ['DEMO103', 'Habilidades Universitarias', 'Formación General', 2],
            ];
            $courses = [];

            foreach ($courseDetails as [$code, $name, $program, $credits]) {
                $courses[] = Course::updateOrCreate(
                    ['codigo_asig' => $code],
                    [
                        'nombre_asig' => $name,
                        'programa_academico' => $program,
                        'creditos' => $credits,
                    ]
                );
            }

            $activityTypes = ['monitoria', 'cultural', 'deportiva'];
            $requestTypes = ['homologacion', 'excusa', 'otra'];
            $riskTypes = ['academico', 'inasistencia', 'bienestar'];
            $riskLevels = ['alto', 'medio', 'bajo'];

            foreach ($students as $index => $student) {
                $slot = $index - 1;
                $period = $periods[$slot];
                $course = $courses[$slot];

                StudentActivity::firstOrCreate(
                    [
                        'id_estudiante' => $student->id_estudiante,
                        'id_periodo' => $period->id_periodo,
                        'nombre_actividad' => sprintf('Actividad de prueba %d', $index),
                    ],
                    [
                        'tipo_actividad' => $activityTypes[$slot],
                        'organizacion_empresa' => 'SAAT - datos de demostración',
                        'rol_o_actividades' => 'Participación de prueba para validar el registro.',
                        'evaluacion' => 'Actividad de demostración registrada.',
                    ]
                );

                AcademicRequest::firstOrCreate(
                    [
                        'id_estudiante' => $student->id_estudiante,
                        'id_periodo' => $period->id_periodo,
                        'tipo_solicitud' => $requestTypes[$slot],
                    ],
                    [
                        'fecha_radicacion' => $period->fecha_inicio,
                        'estado' => ['en_tramite', 'radicada', 'aprobada'][$slot],
                    ]
                );

                Tuition::updateOrCreate(
                    [
                        'id_estudiante' => $student->id_estudiante,
                        'id_asignatura' => $course->id_asignatura,
                        'id_periodo' => $period->id_periodo,
                    ],
                    [
                        'nota_final' => [2.8, 3.7, 4.5][$slot],
                        'estado_materia' => ['reprobada', 'aprobada', 'aprobada'][$slot],
                    ]
                );

                $warning = EarlyWarning::firstOrCreate(
                    [
                        'id_estudiante' => $student->id_estudiante,
                        'id_periodo' => $period->id_periodo,
                        'tipo_riesgo' => $riskTypes[$slot],
                    ],
                    [
                        'nivel_riesgo' => $riskLevels[$slot],
                        'fecha_reporte' => $period->fecha_inicio,
                        'estado' => ['en_seguimiento', 'abierta', 'cerrada'][$slot],
                    ]
                );

                FollowupIntervention::firstOrCreate(
                    [
                        'id_alerta' => $warning->id_alerta,
                        'id_profesor' => $teachers[$index]->id_profesor,
                        'fecha_sesion' => $period->fecha_inicio,
                        'tipo_intervencion' => 'tutoria',
                    ],
                    [
                        'observaciones' => 'Sesión inicial de seguimiento de demostración.',
                        'compromisos' => 'Revisar el avance en la próxima sesión.',
                    ]
                );
            }
        });
    }
}
