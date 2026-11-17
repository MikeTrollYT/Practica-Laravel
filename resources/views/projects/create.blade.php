<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf

                        {{-- Campo: Título --}}
                        <div class="form-group">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="200" class="form-input">
                            @error('title')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Descripción --}}
                        <div class="form-group">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea name="description" id="description" rows="4" required class="form-textarea">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Fecha de vencimiento --}}
                        <div class="form-group">
                            <label for="due_date" class="form-label">Fecha de vencimiento *</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required min="1900-01-01" max="9999-12-31" class="form-input">
                            @error('due_date')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo: Estado --}}
                        <div class="form-group">
                            <label for="status" class="form-label">Estado *</label>
                            <select name="status" id="status" required class="form-select">
                                <option value="">Selecciona un estado</option>
                                <option value="pendiente" {{ old('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_progreso" {{ old('status') == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                <option value="completado" {{ old('status') == 'completado' ? 'selected' : '' }}>Completado</option>
                            </select>
                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-2">
                            <button type="submit" class="btn">
                                Guardar proyecto
                            </button>
                            <a href="{{ route('projects.index') }}" class="btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
