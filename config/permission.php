<?php

return [

    'models' => [

        /*
         * Cuando uses el trait "HasPermissions" de este paquete, necesitamos saber
         * qué modelo Eloquent se usará para obtener tus permisos. Por supuesto,
         * generalmente es el modelo "Permission" pero puedes usar el que prefieras.
         *
         * El modelo que quieras usar como modelo de Permiso debe implementar el
         * contrato `Spatie\Permission\Contracts\Permission`.
         */

        //'permission' => Spatie\Permission\Models\Permission::class,
        'permission' => App\Models\Permission::class,

        /*
         * Cuando uses el trait "HasRoles" de este paquete, necesitamos saber
         * qué modelo Eloquent se usará para obtener tus roles. Por supuesto,
         * generalmente es el modelo "Role" pero puedes usar el que prefieras.
         *
         * El modelo que quieras usar como modelo de Rol debe implementar el
         * contrato `Spatie\Permission\Contracts\Role`.
         */

        //'role' => Spatie\Permission\Models\Role::class,
        'role' => App\Models\Role::class,

    ],

    'table_names' => [

        /*
         * Cuando uses el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla se usará para obtener tus roles. Hemos elegido un valor básico
         * por defecto pero puedes cambiarlo fácilmente a cualquier tabla que desees.
         */

        'roles' => 'roles',

        /*
         * Cuando uses el trait "HasPermissions" de este paquete, necesitamos saber qué
         * tabla se usará para obtener tus permisos. Hemos elegido un valor básico
         * por defecto pero puedes cambiarlo fácilmente a cualquier tabla que desees.
         */

        'permissions' => 'permissions',

        /*
         * Cuando uses el trait "HasPermissions" de este paquete, necesitamos saber qué
         * tabla se usará para obtener los permisos asignados a los modelos. Hemos elegido
         * un valor básico por defecto pero puedes cambiarlo fácilmente a cualquier tabla que desees.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * Cuando uses el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla se usará para obtener los roles asignados a los modelos. Hemos elegido
         * un valor básico por defecto pero puedes cambiarlo fácilmente a cualquier tabla que desees.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * Cuando uses el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla se usará para obtener los permisos asignados a los roles. Hemos elegido
         * un valor básico por defecto pero puedes cambiarlo fácilmente a cualquier tabla que desees.
         */

        'role_has_permissions' => 'role_has_permissions',
        
    ],

    'column_names' => [
        /*
         * Cambia esto si quieres nombrar los pivotes relacionados con nombres diferentes a los predeterminados
         */
        'role_pivot_key' => null, // predeterminado 'role_id',
        'permission_pivot_key' => null, // predeterminado 'permission_id',

        /*
         * Cambia esto si quieres nombrar la clave primaria del modelo relacionada con un nombre distinto a `model_id`.
         *
         * Por ejemplo, esto sería útil si tus claves primarias son UUIDs.
         * En ese caso, podrías nombrarla `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Cambia esto si quieres usar la funcionalidad de equipos (teams) y la clave foránea de tu modelo relacionada
         * es distinta a `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * Cuando está en true, el método para verificar permisos será registrado en el gate.
     * Ponlo en false si quieres implementar lógica personalizada para la verificación de permisos.
     */

    'register_permission_check_method' => true,

    /*
     * Cuando está en true, se registrará un listener para el evento Laravel\Octane\Events\OperationTerminated
     * Esto refrescará los permisos en cada TickTerminated, TaskTerminated y RequestTerminated.
     * NOTA: Esto normalmente no es necesario, pero es útil en combinaciones Octane/Vapor.
     */
    'register_octane_reset_listener' => false,

    /*
     * Se dispararán eventos cuando un rol o permiso sea asignado o removido:
     * \Spatie\Permission\Events\RoleAttached
     * \Spatie\Permission\Events\RoleDetached
     * \Spatie\Permission\Events\PermissionAttached
     * \Spatie\Permission\Events\PermissionDetached
     *
     * Para activarlo, pon esto en true y crea listeners para estos eventos.
     */
    'events_enabled' => false,

    /*
     * Funcionalidad de Equipos (Teams).
     * Cuando está en true, el paquete implementa equipos usando la clave foránea 'team_foreign_key'.
     * Si quieres que las migraciones creen esta clave foránea, ponlo en true antes de migrar.
     * Si ya migraste, tendrás que crear una nueva migración para agregar 'team_foreign_key' a
     * las tablas 'roles', 'model_has_roles' y 'model_has_permissions'
     * (revisa la última versión del archivo de migración de este paquete).
     */

    'teams' => false,

    /*
     * Clase que se usa para resolver el id del equipo (team).
     */
    'team_resolver' => \Spatie\Permission\DefaultTeamResolver::class,

    /*
     * Cliente de Passport para credenciales de cliente
     * Cuando está en true, el paquete usará el cliente de Passport para verificar permisos.
     */

    'use_passport_client_credentials' => false,

    /*
     * Cuando está en true, los nombres de permisos requeridos se agregarán a los mensajes de excepción.
     * Esto podría considerarse una fuga de información en algunos contextos,
     * por lo que el valor predeterminado es false para mayor seguridad.
     */

    'display_permission_in_exception' => false,

    /*
     * Cuando está en true, los nombres de roles requeridos se agregarán a los mensajes de excepción.
     * Esto podría considerarse una fuga de información en algunos contextos,
     * por lo que el valor predeterminado es false para mayor seguridad.
     */

    'display_role_in_exception' => false,

    /*
     * Por defecto, las búsquedas de permisos con comodines están desactivadas.
     * Consulta la documentación para entender la sintaxis soportada.
     */

    'enable_wildcard_permission' => false,

    /*
     * Clase que se usa para interpretar permisos con comodines.
     * Si necesitas modificar los delimitadores, sobreescribe esta clase y especifica su nombre aquí.
     */
    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /* Configuración específica para caché */

    'cache' => [

        /*
         * Por defecto, todos los permisos se almacenan en caché por 24 horas para mejorar el rendimiento.
         * Cuando se actualizan permisos o roles, la caché se vacía automáticamente.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * La clave de caché que se usa para almacenar todos los permisos.
         */

        'key' => 'spatie.permission.cache',

        /*
         * Opcionalmente puedes indicar un driver de caché específico para usar en los permisos y roles,
         * usando cualquiera de los drivers listados en el archivo cache.php.
         * Usar 'default' significa usar el driver por defecto definido en cache.php.
         */

        'store' => 'default',
    ],
];
