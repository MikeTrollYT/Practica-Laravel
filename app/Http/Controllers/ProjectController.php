<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Muestra la lista de todos los proyectos
     */
    public function index()
    {
        // Obtiene todos los proyectos ordenados por fecha de vencimiento
        $projects = Project::with('creator')->orderBy('due_date', 'asc')->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Muestra el formulario para crear un nuevo proyecto
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Guarda un nuevo proyecto en la base de datos
     */
    public function store(Request $request)
    {
        // Validación: title, due_date y status son obligatorios
        $request->validate([
            'title' => 'required|max:200',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|max:50',
        ]);

        // Crea el proyecto asignando automáticamente el user_id del usuario autenticado
        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'user_id' => Auth::id(), // El creador es el usuario actual
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente');
    }

    /**
     * Muestra los detalles de un proyecto específico
     */
    public function show(string $id)
    {
        // findOrFail devuelve 404 si no existe
        // Carga el creador y los participantes (relación N:N con los campos de la tabla pivote)
        $project = Project::with(['creator', 'users'])->findOrFail($id);

        // Si el usuario actual es el propietario, obtiene la lista de usuarios disponibles para añadir
        $availableUsers = null;
        if ($project->user_id === Auth::id()) {
            // Usuarios que NO participan ya en el proyecto y que NO son el propietario
            $availableUsers = \App\Models\User::whereNotIn('id', $project->users->pluck('id'))
                                              ->where('id', '!=', $project->user_id)
                                              ->get();
        }

        return view('projects.show', compact('project', 'availableUsers'));
    }

    /**
     * Muestra el formulario para editar un proyecto
     * Solo el propietario puede acceder
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este proyecto');
        }

        return view('projects.edit', compact('project'));
    }

    /**
     * Actualiza un proyecto existente
     * Solo el propietario puede actualizarlo
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para actualizar este proyecto');
        }

        // Validación
        $request->validate([
            'title' => 'required|max:200',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|max:50',
        ]);

        // Actualiza el proyecto (no se cambia el user_id)
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Proyecto actualizado correctamente');
    }

    /**
     * Elimina un proyecto
     * Solo el propietario puede borrarlo
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este proyecto');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado correctamente');
    }

    /**
     * Añade un participante al proyecto (relación N:N con attach)
     * Solo el propietario puede añadir participantes
     */
    public function addParticipant(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para añadir participantes a este proyecto');
        }

        // Validación: user_id y role son obligatorios
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|max:50',
        ]);

        // Añade el participante con attach() - los campos adicionales de la tabla pivote
        $project->users()->attach($request->user_id, [
            'role' => $request->role,
            'contribution_hours' => 0, // Inicia en 0 horas
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Participante añadido correctamente');
    }

    /**
     * Actualiza las horas de contribución de un participante (updateExistingPivot)
     * Solo el propietario puede actualizar
     */
    public function updateParticipantHours(Request $request, string $projectId, string $userId)
    {
        $project = Project::findOrFail($projectId);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para actualizar participantes de este proyecto');
        }

        // Validación: horas deben ser numéricas y no negativas
        $request->validate([
            'contribution_hours' => 'required|numeric|min:0',
        ]);

        // Actualiza solo el campo contribution_hours de la tabla pivote
        $project->users()->updateExistingPivot($userId, [
            'contribution_hours' => $request->contribution_hours,
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Horas actualizadas correctamente');
    }

    /**
     * Elimina un participante del proyecto (detach)
     * Solo el propietario puede quitar participantes
     */
    public function removeParticipant(string $projectId, string $userId)
    {
        $project = Project::findOrFail($projectId);

        // Comprueba que el usuario actual es el creador
        if ($project->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para quitar participantes de este proyecto');
        }

        // Elimina la relación con detach()
        $project->users()->detach($userId);

        return redirect()->route('projects.show', $project->id)->with('success', 'Participante eliminado correctamente');
    }
}
