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
