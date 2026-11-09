<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Mensaje de éxito --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Detalles del proyecto --}}
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-4">{{ $project->title }}</h3>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700">Descripción:</p>
                            <p class="text-gray-600">{{ $project->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Fecha de vencimiento:</p>
                                <p class="text-gray-600">{{ $project->due_date }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Estado:</p>
                                <p class="text-gray-600">{{ $project->status }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700">Creador:</p>
                            <p class="text-gray-600">{{ $project->creator->name }} ({{ $project->creator->email }})</p>
                        </div>

                        <div class="text-sm text-gray-500">
                            <p>Creado el: {{ $project->created_at->format('d/m/Y H:i') }}</p>
                            <p>Última actualización: {{ $project->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Sección de participantes (relación N:N) --}}
                    <div class="mb-6 border-t pt-6">
                        <h4 class="text-xl font-bold mb-4">Participantes del proyecto</h4>

                        {{-- Listado de participantes actuales --}}
                        @if($project->users->count() > 0)
                            <div class="mb-6">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Horas</th>
                                            @if($project->user_id === Auth::id())
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($project->users as $participant)
                                            <tr>
                                                <td class="px-4 py-2">{{ $participant->name }}</td>
                                                <td class="px-4 py-2">{{ $participant->pivot->role }}</td>
                                                <td class="px-4 py-2">{{ $participant->pivot->contribution_hours }} h</td>

                                                {{-- Solo el propietario puede editar horas y quitar participantes --}}
                                                @if($project->user_id === Auth::id())
                                                    <td class="px-4 py-2">
                                                        <div class="flex gap-2 items-center">
                                                            {{-- Formulario para actualizar horas --}}
                                                            <form method="POST" action="{{ route('projects.participants.update', [$project->id, $participant->id]) }}" class="flex gap-1 items-center">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="number" name="contribution_hours" value="{{ $participant->pivot->contribution_hours }}" min="0" step="1" required
                                                                       class="w-20 px-2 py-1 text-sm border border-gray-300 rounded">
                                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                    Actualizar
                                                                </button>
                                                            </form>

                                                            {{-- Formulario para quitar participante --}}
                                                            <form method="POST" action="{{ route('projects.participants.remove', [$project->id, $participant->id]) }}" onsubmit="return confirm('¿Quitar a este participante?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                    Quitar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 mb-4">No hay participantes asignados todavía.</p>
                        @endif

                        {{-- Formulario para añadir participante (solo el propietario) --}}
                        @if($project->user_id === Auth::id())
                            <div class="bg-gray-50 p-4 rounded">
                                <h5 class="font-semibold mb-2">Añadir participante</h5>

                                @if($availableUsers && $availableUsers->count() > 0)
                                    <form method="POST" action="{{ route('projects.participants.add', $project->id) }}" class="flex gap-2 items-end">
                                        @csrf

                                        <div class="flex-1">
                                            <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                                            <select name="user_id" id="user_id" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Selecciona un usuario</option>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <span class="text-red-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="flex-1">
                                            <label for="role" class="block text-sm font-medium text-gray-700">Rol *</label>
                                            <input type="text" name="role" id="role" value="{{ old('role') }}" required maxlength="50"
                                                   placeholder="Ej: Desarrollador, Diseñador..."
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('role')
                                                <span class="text-red-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Añadir
                                        </button>
                                    </form>
                                @else
                                    <p class="text-gray-500 text-sm">No hay más usuarios disponibles para añadir.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex gap-2">
                        <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al listado
                        </a>

                        {{-- Botones de editar/borrar solo para el propietario --}}
                        @if($project->user_id === Auth::id())
                            <a href="{{ route('projects.edit', $project->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('projects.destroy', $project->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar este proyecto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Borrar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
