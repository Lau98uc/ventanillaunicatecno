<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SolicitudTramite;
use App\Models\Tramite;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowEjecucion;
use App\Models\WorkflowPaso;
use App\Models\WorkflowTransicion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // 1 Unidades
        $unidadDG = Unidad::create([
            'name' => 'Dirección General',
            'phone' => '+591 2123456',
            'address' => 'Av. Principal 123',
            'city' => 'La Paz',
            'region' => 'LP',
            'country' => 'BO',
        ]);

        $unidadRRHH = Unidad::create([
            'name' => 'Recursos Humanos',
            'phone' => '+591 2789012',
            'address' => 'Calle Recursos 456',
            'city' => 'Cochabamba',
            'region' => 'CB',
            'country' => 'BO',
        ]);

        $unidadTI = Unidad::create([
            'name' => 'Tecnologías de Información',
            'phone' => '+591 2654321',
            'address' => 'Zona IT, Piso 3',
            'city' => 'Santa Cruz',
            'region' => 'SC',
            'country' => 'BO',
        ]);

        // 2. Users
        // Inicializar Faker
        $faker = Faker::create();

        // 2. Usuarios
        $usuario1 = User::create([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'remember_token' => Str::random(10),
            'unidad_id' => $unidadDG->id
        ]);

        $usuario2 = User::create([
            'first_name' => 'Carlos',
            'last_name' => 'Gómez',
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'remember_token' => Str::random(10),
            'unidad_id' => $unidadRRHH->id
        ]);

        $usuario3 = User::create([
            'first_name' => 'Laura',
            'last_name' => 'Méndez',
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'remember_token' => Str::random(10),
            'unidad_id' => $unidadTI->id
        ]);

         $rol1 = Role::create(
            ['name' => 'ASISTENTE ADMINISTRATIVA', 'codigo' => 'NA 16-20', 'guard_name' => 'web'],
        );

        $rol2 = Role::create(['name' => 'APOYO ADMINISTRATIVO', 'codigo' => 'NA 18-20', 'guard_name' => 'web'],);

        // Trámites Académicos
        $this->crearTramiteCompleto('Inscripción a materias / semestre', 'Trámite para inscribirse en materias del semestre.', $unidadDG, $unidadRRHH, $usuario1, $rol1, $rol2);
        $this->crearTramiteCompleto('Retiro o adición de materias', 'Trámite para retirar o adicionar materias.', $unidadRRHH, $unidadTI, $usuario2, $rol1, $rol2);
        $this->crearTramiteCompleto('Solicitud de equivalencias de materias', 'Equivalencia de materias previamente cursadas.', $unidadTI, $unidadDG, $usuario3, $rol1, $rol2);

        // Trámites Administrativos
        $this->crearTramiteCompleto('Emisión de certificados', 'Certificados de estudiante regular, egresado, etc.', $unidadDG, $unidadRRHH, $usuario1, $rol1, $rol2);
        $this->crearTramiteCompleto('Trámite de título', 'Trámite para obtener el título académico.', $unidadRRHH, $unidadTI, $usuario2, $rol1, $rol2);

        // Trámites Estudiantiles Técnicos
        $this->crearTramiteCompleto('Solicitud de correo institucional', 'Asignación de correo institucional.', $unidadTI, $unidadTI, $usuario2, $rol1, $rol2);
        $this->crearTramiteCompleto('Acceso a laboratorios', 'Solicitud para usar laboratorios físicos o virtuales.', $unidadTI, $unidadDG, $usuario3, $rol1, $rol2);

        // Trámites para Docentes
        $this->crearTramiteCompleto('Registro o actualización de carga horaria', 'Asignación y modificación de carga docente.', $unidadRRHH, $unidadTI, $usuario2, $rol1, $rol2);



        // Insertar roles hijos SIN rol_padre_id pero con codigo y guard_name
        DB::table('roles')->insert([

            ['name' => 'SECRETARÍA GENERAL', 'codigo' => 'NA 07', 'guard_name' => 'web'],
            ['name' => 'APOYO PROFESIONAL III', 'codigo' => 'NA 09', 'guard_name' => 'web'],

            ['name' => 'ENC. ACT. FIJO FAC.', 'codigo' => 'NA 16-20', 'guard_name' => 'web'],
            ['name' => 'ASISTENTE JURÍDICO', 'codigo' => 'NA 07-09', 'guard_name' => 'web'],
            ['name' => 'AUXILIAR JURÍDICO', 'codigo' => 'NA 13-15', 'guard_name' => 'web'],
            ['name' => 'APOYO PROFESIONAL I', 'codigo' => 'NA 07-09', 'guard_name' => 'web'],
            ['name' => 'RESPONSABLE DE PROYECTOS IDH', 'codigo' => 'NA 07-09', 'guard_name' => 'web'],
            ['name' => 'RELACIONES PÚBLICAS Y', 'codigo' => 'NA 07-09', 'guard_name' => 'web'],

            ['name' => 'COORD. DE ARTE', 'codigo' => 'A 13-15', 'guard_name' => 'web'],
            ['name' => 'SOCIAL MEDIA M', 'codigo' => 'NA 16-18', 'guard_name' => 'web'],
            ['name' => 'COORD. FÍSICA', 'codigo' => null, 'guard_name' => 'web'],
            ['name' => 'COORD. MATEMÁTICA', 'codigo' => null, 'guard_name' => 'web'],
            ['name' => 'COORD. PROGRAMACIÓN', 'codigo' => null, 'guard_name' => 'web'],
            ['name' => 'COORD. SEGUIMIENTO CURRICULAR', 'codigo' => 'NA 08-09', 'guard_name' => 'web'],
            ['name' => 'COORD. UNIDAD DE BIENESTAR SOCIAL FAC.', 'codigo' => 'NA 07-09', 'guard_name' => 'web'],
            ['name' => 'CPD FACULTATIVO', 'codigo' => 'NA 10-12', 'guard_name' => 'web'],

        ]);


        $this->call(\Database\Seeders\PermissionSeeder::class);
    }

    private function crearTramiteCompleto($nombreTramite, $descripcionWorkflow, $unidad1, $unidad2, $usuario, $rol1, $rol2)
    {
        $workflow = Workflow::create([
            'nombre' => $nombreTramite,
            'descripcion' => $descripcionWorkflow
        ]);

        $tramite = Tramite::create([
            'nombre' => $nombreTramite,
            'workflow_id' => $workflow->id
        ]);

        $paso1 = WorkflowPaso::create([
            'workflow_id' => $workflow->id,
            'nombre' => 'Inicio de Solicitud',
            'orden' => 1,
            'role_id' => $rol1->id,
        ]);

        $paso2 = WorkflowPaso::create([
            'workflow_id' => $workflow->id,
            'nombre' => 'Revisión Administrativa',
            'orden' => 2,
            'role_id' => $rol2->id,
        ]);

        $paso3 = WorkflowPaso::create([
            'workflow_id' => $workflow->id,
            'nombre' => 'Aprobación Final',
            'orden' => 3,
            'role_id' => $rol2->id,
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso1->id,
            'paso_destino_id' => $paso2->id,
            'accion' => 'enviar',
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso2->id,
            'paso_destino_id' => $paso3->id,
            'accion' => 'aprobar',
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso2->id,
            'paso_destino_id' => null,
            'accion' => 'rechazar',
        ]);

        $solicitud = SolicitudTramite::create([
            'tramite_id' => $tramite->id,
            'usuario_id' => $usuario->id,
            'estado_actual' => $paso1->nombre
        ]);

        WorkflowEjecucion::create([
            'solicitud_id' => $solicitud->id,
            'paso_id' => $paso1->id,
            'usuario_id' => $usuario->id,
            'estado' => 'completado',
            'fecha_inicio' => Carbon::now()->subDays(2),
            'fecha_fin' => Carbon::now()->subDay(),
        ]);

        WorkflowEjecucion::create([
            'solicitud_id' => $solicitud->id,
            'paso_id' => $paso2->id,
            'estado' => 'pendiente',
        ]);
    }
}
