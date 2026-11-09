<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proyectos
            </h2>
            <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Listado de proyectos --}}
                    @forelse($projects as $project)
                        <div class="border-b border-gray-200 py-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold">
                                        <a href="{{ route('projects.show', $project->id) }}" class="text-blue-600 hover:underline">
                                            {{ $project->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($project->description, 100) }}</p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <strong>Vencimiento:</strong> {{ $project->due_date }} |
                                        <strong>Estado:</strong> {{ $project->status }} |
                                        <strong>Creador:</strong> {{ $project->creator->name }}
                                    </p>
                                </div>

                                {{-- Botones de editar/borrar solo para el propietario --}}
                                @if($project->user_id === Auth::id())
                                    <div class="ml-4 flex gap-2">
                                        <a href="{{ route('projects.edit', $project->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('projects.destroy', $project->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar este proyecto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                Borrar
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No hay proyectos todavía. <a href="{{ route('projects.create') }}" class="text-blue-600 hover:underline">Crea uno</a>.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
