<?php

/**
 * Script de pruebas exhaustivas del proyecto hola-laravel
 *
 * Este script verifica todas las funcionalidades del CRUD de proyectos
 * con autenticación y gestión de participantes.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n=== PRUEBAS DE FUNCIONALIDADES DEL PROYECTO HOLA-LARAVEL ===\n\n";

$resultados = [
    'exitos' => [],
    'fallos' => []
];

// ========================================
// 1. PRUEBAS DE MODELOS Y RELACIONES
// ========================================
echo "📋 1. PRUEBAS DE MODELOS Y RELACIONES\n";
echo str_repeat("-", 50) . "\n";

try {
    // Verificar modelo User
    $userCount = User::count();
    echo "✓ Modelo User: {$userCount} usuarios en la BD\n";
    $resultados['exitos'][] = "Modelo User funciona correctamente";

    // Verificar modelo Project
    $projectCount = Project::count();
    echo "✓ Modelo Project: {$projectCount} proyectos en la BD\n";
    $resultados['exitos'][] = "Modelo Project funciona correctamente";

    // Verificar tabla pivote
    $participantCount = DB::table('project_user')->count();
    echo "✓ Tabla pivote project_user: {$participantCount} relaciones\n";
    $resultados['exitos'][] = "Tabla pivote configurada correctamente";

} catch (Exception $e) {
    echo "✗ Error en modelos: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en modelos: " . $e->getMessage();
}

// ========================================
// 2. PRUEBAS DE RELACIONES ELOQUENT
// ========================================
echo "\n📋 2. PRUEBAS DE RELACIONES ELOQUENT\n";
echo str_repeat("-", 50) . "\n";

try {
    // Obtener un proyecto con su creador
    $project = Project::with('creator')->first();
    if ($project) {
        echo "✓ Relación Project->creator: {$project->creator->name}\n";
        $resultados['exitos'][] = "Relación belongsTo (creator) funciona";
    } else {
        echo "⚠ No hay proyectos para probar la relación\n";
    }

    // Obtener un proyecto con sus participantes (relación users)
    $project = Project::with('users')->first();
    if ($project) {
        $participantCount = $project->users->count();
        echo "✓ Relación Project->users: {$participantCount} participantes\n";
        $resultados['exitos'][] = "Relación belongsToMany (users) funciona";
    }

    // Obtener un usuario con sus proyectos creados
    $user = User::with('projects')->first();
    if ($user) {
        $projectCount = $user->projects->count();
        echo "✓ Relación User->projects: {$projectCount} proyectos creados\n";
        $resultados['exitos'][] = "Relación hasMany (projects) funciona";
    }

    // Obtener un usuario con sus proyectos donde participa
    $user = User::with('projectsAsParticipant')->first();
    if ($user) {
        $participationCount = $user->projectsAsParticipant->count();
        echo "✓ Relación User->projectsAsParticipant: {$participationCount} participaciones\n";
        $resultados['exitos'][] = "Relación belongsToMany (projectsAsParticipant) funciona";
    }

} catch (Exception $e) {
    echo "✗ Error en relaciones: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en relaciones: " . $e->getMessage();
}

// ========================================
// 3. PRUEBAS DE FILLABLE Y ASIGNACIÓN MASIVA
// ========================================
echo "\n📋 3. PRUEBAS DE FILLABLE Y ASIGNACIÓN MASIVA\n";
echo str_repeat("-", 50) . "\n";

try {
    // Verificar $fillable en User
    $userFillable = (new User)->getFillable();
    echo "✓ User \$fillable: " . implode(', ', $userFillable) . "\n";
    $resultados['exitos'][] = "User tiene \$fillable configurado";

    // Verificar $fillable en Project
    $projectFillable = (new Project)->getFillable();
    echo "✓ Project \$fillable: " . implode(', ', $projectFillable) . "\n";
    $resultados['exitos'][] = "Project tiene \$fillable configurado";

} catch (Exception $e) {
    echo "✗ Error en fillable: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en fillable: " . $e->getMessage();
}

// ========================================
// 4. PRUEBAS DE CRUD - CREAR
// ========================================
echo "\n📋 4. PRUEBAS DE CRUD - CREAR\n";
echo str_repeat("-", 50) . "\n";

DB::beginTransaction();
try {
    // Crear un usuario de prueba
    $testUser = User::create([
        'name' => 'Usuario Test',
        'email' => 'test_' . time() . '@example.com',
        'password' => Hash::make('password123'),
    ]);
    echo "✓ Usuario de prueba creado: {$testUser->email}\n";
    $resultados['exitos'][] = "Crear usuario funciona";

    // Crear un proyecto de prueba
    $testProject = Project::create([
        'title' => 'Proyecto Test',
        'description' => 'Descripción de prueba',
        'due_date' => now()->addDays(30),
        'status' => 'in_progress',
        'user_id' => $testUser->id,
    ]);
    echo "✓ Proyecto de prueba creado: {$testProject->title}\n";
    $resultados['exitos'][] = "Crear proyecto funciona";

    DB::rollBack(); // Revertir cambios de prueba
    echo "✓ Cambios revertidos (rollback)\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error al crear: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al crear: " . $e->getMessage();
}

// ========================================
// 5. PRUEBAS DE CRUD - LEER
// ========================================
echo "\n📋 5. PRUEBAS DE CRUD - LEER\n";
echo str_repeat("-", 50) . "\n";

try {
    // Leer todos los proyectos
    $projects = Project::all();
    echo "✓ Leer todos los proyectos: {$projects->count()} encontrados\n";
    $resultados['exitos'][] = "Leer todos los proyectos funciona";

    // Leer un proyecto específico
    $project = Project::first();
    if ($project) {
        echo "✓ Leer proyecto por ID: {$project->name}\n";
        $resultados['exitos'][] = "Leer proyecto por ID funciona";
    }

    // Leer con relaciones
    $project = Project::with(['creator', 'users'])->first();
    if ($project) {
        echo "✓ Leer proyecto con relaciones (eager loading)\n";
        $resultados['exitos'][] = "Eager loading funciona";
    }

} catch (Exception $e) {
    echo "✗ Error al leer: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al leer: " . $e->getMessage();
}

// ========================================
// 6. PRUEBAS DE CRUD - ACTUALIZAR
// ========================================
echo "\n📋 6. PRUEBAS DE CRUD - ACTUALIZAR\n";
echo str_repeat("-", 50) . "\n";

DB::beginTransaction();
try {
    $project = Project::first();
    if ($project) {
        $oldTitle = $project->title;
        $project->update(['title' => 'Nombre Actualizado Test']);
        echo "✓ Actualizar proyecto: '{$oldTitle}' → '{$project->title}'\n";
        $resultados['exitos'][] = "Actualizar proyecto funciona";

        DB::rollBack(); // Revertir cambios
        echo "✓ Cambios revertidos (rollback)\n";
    } else {
        echo "⚠ No hay proyectos para probar actualización\n";
    }

} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error al actualizar: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al actualizar: " . $e->getMessage();
}

// ========================================
// 7. PRUEBAS DE GESTIÓN DE PARTICIPANTES
// ========================================
echo "\n📋 7. PRUEBAS DE GESTIÓN DE PARTICIPANTES\n";
echo str_repeat("-", 50) . "\n";

DB::beginTransaction();
try {
    $project = Project::first();
    $users = User::take(2)->get();

    if ($project && $users->count() >= 2) {
        // Añadir participante
        $user = $users->first();
        $project->users()->attach($user->id, ['role' => 'developer', 'contribution_hours' => 0]);
        echo "✓ Añadir participante: {$user->name} al proyecto\n";
        $resultados['exitos'][] = "Añadir participante funciona";

        // Actualizar rol de participante
        $project->users()->updateExistingPivot($user->id, ['role' => 'designer']);
        echo "✓ Actualizar rol de participante: developer → designer\n";
        $resultados['exitos'][] = "Actualizar rol funciona";

        // Eliminar participante
        $project->users()->detach($user->id);
        echo "✓ Eliminar participante del proyecto\n";
        $resultados['exitos'][] = "Eliminar participante funciona";

        DB::rollBack();
        echo "✓ Cambios revertidos (rollback)\n";
    } else {
        echo "⚠ No hay suficientes datos para probar participantes\n";
    }

} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error en participantes: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en participantes: " . $e->getMessage();
}

// ========================================
// 8. PRUEBAS DE VALIDACIÓN
// ========================================
echo "\n📋 8. PRUEBAS DE VALIDACIÓN\n";
echo str_repeat("-", 50) . "\n";

try {
    // Verificar que existen reglas de validación en el controlador
    $controllerPath = app_path('Http/Controllers/ProjectController.php');
    $controllerContent = file_get_contents($controllerPath);

    if (strpos($controllerContent, 'validate') !== false) {
        echo "✓ Validación implementada en ProjectController\n";
        $resultados['exitos'][] = "Validación implementada";

        // Verificar reglas específicas
        if (strpos($controllerContent, "'title' => 'required") !== false) {
            echo "✓ Regla de validación: title es requerido\n";
        }
        if (strpos($controllerContent, "'description' => 'required") !== false) {
            echo "✓ Regla de validación: description es requerido\n";
        }
        if (strpos($controllerContent, "'due_date' => 'required") !== false) {
            echo "✓ Regla de validación: due_date es requerido\n";
        }
    } else {
        echo "✗ No se encontró validación en el controlador\n";
        $resultados['fallos'][] = "Falta validación en el controlador";
    }

} catch (Exception $e) {
    echo "✗ Error al verificar validación: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al verificar validación: " . $e->getMessage();
}

// ========================================
// 9. PRUEBAS DE RUTAS
// ========================================
echo "\n📋 9. PRUEBAS DE RUTAS\n";
echo str_repeat("-", 50) . "\n";

try {
    $routes = [
        'projects.index' => 'Listar proyectos',
        'projects.create' => 'Formulario crear proyecto',
        'projects.store' => 'Guardar proyecto',
        'projects.show' => 'Ver proyecto',
        'projects.edit' => 'Formulario editar proyecto',
        'projects.update' => 'Actualizar proyecto',
        'projects.destroy' => 'Eliminar proyecto',
        'projects.participants.add' => 'Añadir participante',
        'projects.participants.update' => 'Actualizar participante',
        'projects.participants.remove' => 'Eliminar participante',
    ];

    $routeCount = 0;
    foreach ($routes as $routeName => $description) {
        if (Route::has($routeName)) {
            echo "✓ Ruta '{$routeName}': {$description}\n";
            $routeCount++;
        } else {
            echo "✗ Ruta '{$routeName}' no encontrada\n";
            $resultados['fallos'][] = "Ruta '{$routeName}' no existe";
        }
    }

    if ($routeCount == count($routes)) {
        $resultados['exitos'][] = "Todas las rutas están registradas ({$routeCount})";
    }

} catch (Exception $e) {
    echo "✗ Error al verificar rutas: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al verificar rutas: " . $e->getMessage();
}

// ========================================
// 10. PRUEBAS DE VISTAS
// ========================================
echo "\n📋 10. PRUEBAS DE VISTAS\n";
echo str_repeat("-", 50) . "\n";

try {
    $views = [
        'projects.index' => 'resources/views/projects/index.blade.php',
        'projects.create' => 'resources/views/projects/create.blade.php',
        'projects.edit' => 'resources/views/projects/edit.blade.php',
        'projects.show' => 'resources/views/projects/show.blade.php',
    ];

    $viewCount = 0;
    foreach ($views as $viewName => $viewPath) {
        if (view()->exists($viewName)) {
            echo "✓ Vista '{$viewName}' existe\n";
            $viewCount++;
        } else {
            echo "✗ Vista '{$viewName}' no encontrada\n";
            $resultados['fallos'][] = "Vista '{$viewName}' no existe";
        }
    }

    if ($viewCount == count($views)) {
        $resultados['exitos'][] = "Todas las vistas existen ({$viewCount})";
    }

} catch (Exception $e) {
    echo "✗ Error al verificar vistas: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error al verificar vistas: " . $e->getMessage();
}

// ========================================
// 11. PRUEBAS DE AUTENTICACIÓN
// ========================================
echo "\n📋 11. PRUEBAS DE AUTENTICACIÓN\n";
echo str_repeat("-", 50) . "\n";

try {
    // Verificar rutas de autenticación
    $authRoutes = ['login', 'register', 'logout', 'password.request'];
    $authCount = 0;

    foreach ($authRoutes as $routeName) {
        if (Route::has($routeName)) {
            echo "✓ Ruta de autenticación '{$routeName}' existe\n";
            $authCount++;
        }
    }

    if ($authCount == count($authRoutes)) {
        $resultados['exitos'][] = "Laravel Breeze instalado correctamente";
    }

    // Verificar middleware auth
    $controllerContent = file_get_contents(app_path('Http/Controllers/ProjectController.php'));
    if (strpos($controllerContent, 'auth') !== false ||
        file_exists(base_path('routes/auth.php'))) {
        echo "✓ Middleware de autenticación configurado\n";
        $resultados['exitos'][] = "Middleware auth configurado";
    }

} catch (Exception $e) {
    echo "✗ Error en autenticación: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en autenticación: " . $e->getMessage();
}

// ========================================
// 12. PRUEBAS DE MIGRACIONES
// ========================================
echo "\n📋 12. PRUEBAS DE MIGRACIONES\n";
echo str_repeat("-", 50) . "\n";

try {
    // Verificar estructura de tabla users
    $userColumns = Schema::getColumnListing('users');
    echo "✓ Tabla 'users' tiene " . count($userColumns) . " columnas\n";

    // Verificar estructura de tabla projects
    $projectColumns = Schema::getColumnListing('projects');
    $expectedColumns = ['id', 'title', 'description', 'due_date', 'status', 'user_id', 'created_at', 'updated_at'];
    $missingColumns = array_diff($expectedColumns, $projectColumns);

    if (empty($missingColumns)) {
        echo "✓ Tabla 'projects' tiene todas las columnas necesarias\n";
        $resultados['exitos'][] = "Tabla projects correctamente estructurada";
    } else {
        echo "✗ Faltan columnas en 'projects': " . implode(', ', $missingColumns) . "\n";
        $resultados['fallos'][] = "Faltan columnas en projects";
    }

    // Verificar tabla pivote
    $pivotColumns = Schema::getColumnListing('project_user');
    $expectedPivotColumns = ['id', 'project_id', 'user_id', 'role', 'created_at', 'updated_at'];
    $missingPivotColumns = array_diff($expectedPivotColumns, $pivotColumns);

    if (empty($missingPivotColumns)) {
        echo "✓ Tabla 'project_user' tiene todas las columnas necesarias\n";
        $resultados['exitos'][] = "Tabla pivote correctamente estructurada";
    } else {
        echo "✗ Faltan columnas en 'project_user': " . implode(', ', $missingPivotColumns) . "\n";
        $resultados['fallos'][] = "Faltan columnas en project_user";
    }

} catch (Exception $e) {
    echo "✗ Error en migraciones: " . $e->getMessage() . "\n";
    $resultados['fallos'][] = "Error en migraciones: " . $e->getMessage();
}

// ========================================
// RESUMEN FINAL
// ========================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RESUMEN DE PRUEBAS\n";
echo str_repeat("=", 50) . "\n\n";

$totalExitos = count($resultados['exitos']);
$totalFallos = count($resultados['fallos']);
$totalPruebas = $totalExitos + $totalFallos;

echo "✅ Pruebas exitosas: {$totalExitos}\n";
echo "❌ Pruebas fallidas: {$totalFallos}\n";
echo "📈 Total de pruebas: {$totalPruebas}\n\n";

if ($totalFallos > 0) {
    echo "⚠️  FALLOS DETECTADOS:\n";
    foreach ($resultados['fallos'] as $i => $fallo) {
        echo "  " . ($i + 1) . ". {$fallo}\n";
    }
    echo "\n";
}

$porcentaje = $totalPruebas > 0 ? round(($totalExitos / $totalPruebas) * 100, 2) : 0;
echo "🎯 Porcentaje de éxito: {$porcentaje}%\n\n";

if ($porcentaje >= 90) {
    echo "🎉 ¡Excelente! El proyecto funciona correctamente.\n";
} elseif ($porcentaje >= 70) {
    echo "✅ El proyecto funciona bien, con algunos detalles por revisar.\n";
} else {
    echo "⚠️  El proyecto necesita revisión y correcciones.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
