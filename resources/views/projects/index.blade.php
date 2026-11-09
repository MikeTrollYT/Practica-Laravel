<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proyectos
            </h2>
            <a href="{{ route('projects.create') }}" class="btn">
                Crear Proyecto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Mensaje de éxito (flash) --}}
                    @if(session('success'))
                        <div class="alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Listado de proyectos --}}
                    @forelse($projects as $project)
                        <div class="project-card">
                            <h3>
                                <a href="{{ route('projects.show', $project->id) }}" class="project-title">
                                    {{ $project->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($project->description, 100) }}</p>
                            <p class="project-meta">
                                <strong>Vencimiento:</strong> {{ $project->due_date }} |
                                <strong>Estado:</strong> {{ $project->status }} |
                                <strong>Creador:</strong> {{ $project->creator->name }}
                            </p>

                            {{-- Botones de editar/borrar solo para el propietario --}}
                            @if($project->user_id === Auth::id())
                                <div class="project-actions">
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn-warning btn-sm">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('projects.destroy', $project->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar este proyecto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">No hay proyectos todavía. <a href="{{ route('projects.create') }}" class="text-blue-600 hover:underline">Crea uno</a>.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
