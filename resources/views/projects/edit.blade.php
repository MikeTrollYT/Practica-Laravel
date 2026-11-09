<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Campo: Título --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" required maxlength="200"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Descripción --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción *</label>
                            <textarea name="description" id="description" rows="4" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Fecha de vencimiento --}}
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Fecha de vencimiento *</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $project->due_date) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('due_date')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Estado --}}
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecciona un estado</option>
                                <option value="pendiente" {{ old('status', $project->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_progreso" {{ old('status', $project->status) == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                <option value="completado" {{ old('status', $project->status) == 'completado' ? 'selected' : '' }}>Completado</option>
                            </select>
                            @error('status')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar proyecto
                            </button>
                            <a href="{{ route('projects.show', $project->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
