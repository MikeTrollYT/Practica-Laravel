# Proyecto: hola-laravel

Aplicación de prácticas del curso de Laravel (CRUD de productos con categorías y autenticación).
Sigue siempre las convenciones y la forma de trabajar descritas en este archivo, que reflejan lo que se enseña en el curso.

## Contexto y forma de trabajar

- Soy estudiante: el código que escribas debe ser **sencillo, didáctico y fiel a lo que se explica en el curso**. No uses patrones avanzados (repositorios, servicios, DTOs, Livewire, Inertia, etc.) salvo que lo pida expresamente.
- **Explica brevemente qué hace cada cosa** que añadas (rutas, métodos, directivas Blade, migraciones...). Prefiero entenderlo antes que copiarlo.
- Idioma: responde y comenta el código en **español**. Nombres de clases, tablas, columnas, rutas y variables en **inglés** (como en las prácticas: `Product`, `products`, `name`, `price`).
- Antes de hacer cambios grandes, di qué archivos vas a tocar.
- Si algo del enunciado no está claro, pregunta en vez de inventar.

## Stack

- Laravel 13 (requiere PHP 8.3 o superior)
- Entorno: **Docker con Laravel Sail** (PHP, Composer, Node y MySQL van dentro de contenedores)
- Base de datos: MySQL (servicio de Sail). Las versiones recientes de Laravel usan SQLite por defecto, así que `.env` debe tener `DB_CONNECTION=mysql`
- Vistas: **Blade** (renderizado en servidor, arquitectura MVC)
- Frontend: Vite + Tailwind (se cargan con `@vite(['resources/css/app.css', 'resources/js/app.js'])`)
- Autenticación: **Laravel Breeze** (stack Blade)
- ORM: **Eloquent** (QueryBuilder solo si Eloquent no basta; SQL crudo casi nunca)

## Comandos (SIEMPRE con Sail)

PHP, Composer, Node y MySQL **no están instalados en la máquina**, así que nunca ejecutes `php`, `artisan`, `npm` o `composer` directamente. Usa el prefijo `./vendor/bin/sail`:

```bash
./vendor/bin/sail up -d                       # Levantar contenedores
./vendor/bin/sail down                        # Pararlos
./vendor/bin/sail php --version               # Versión de PHP del contenedor
./vendor/bin/sail artisan <comando>           # Artisan
./vendor/bin/sail artisan route:list          # Ver rutas registradas
./vendor/bin/sail artisan route:clear         # Limpiar caché de rutas
./vendor/bin/sail artisan migrate             # Lanzar migraciones pendientes
./vendor/bin/sail artisan migrate:fresh --seed  # Reconstruir BD y cargar seeders
./vendor/bin/sail artisan tinker              # Consola interactiva
./vendor/bin/sail mysql                       # Consola de MySQL
./vendor/bin/sail npm run dev                 # Vite en vivo mientras se edita CSS/JS
./vendor/bin/sail npm run build               # Compilar versión final de CSS/JS
./vendor/bin/sail composer require <paquete>  # Instalar dependencias PHP
```

- La app se ve en `http://localhost`.
- Comprueba que los contenedores están levantados antes de lanzar comandos artisan.
- `migrate:fresh`, `migrate:reset`, `migrate:rollback` y `db:wipe` son **destructivos**: avísame antes de ejecutarlos.

## Estructura del proyecto

- `app/Http/Controllers/` controladores · `app/Models/` modelos Eloquent
- `routes/web.php` rutas web · `routes/auth.php` rutas de Breeze (no tocar salvo necesidad)
- `resources/views/` vistas Blade · `resources/views/layouts/app.blade.php` master layout
- `resources/views/productos/` vistas del recurso products (`index`, `create`, `edit`, `show`)
- `database/migrations/` migraciones · `database/seeders/` seeders
- `config/`, `bootstrap/`, `storage/`, `vendor/`: **no editar** (salvo `config/` si es imprescindible)
- `.env` contiene datos sensibles: **nunca** lo subas a Git ni lo muestres. Verifica que está en `.gitignore`.

## Convenciones de Laravel (convención sobre configuración)

Respeta las convenciones para no tener que configurar nada extra:

- Tabla en **plural y snake_case** (`products`, `categories`); modelo en **singular y CamelCase** (`Product`, `Category`).
- Controlador en singular + `Controller` (`ProductController`).
- Clave primaria `id`; clave foránea `<modelo_en_singular>_id` (`category_id`).
- Tabla pivote en N:N: nombres de los dos modelos en singular, snake_case y **orden alfabético** (`category_product`).
- Si hay que salirse de la convención, indícalo explícitamente (`$table`, `$primaryKey`, segundo parámetro en `hasMany`/`belongsTo`, etc.) y avísame.

## Rutas

- Todas en `routes/web.php`. Importa siempre el controlador con `use`.
- Sintaxis: `Route::get('/ruta', [MiController::class, 'metodo'])`.
- **Pon nombre a las rutas** con `->name('...')` y en el código/vistas usa `route('nombre')`, nunca URLs a mano.
- Para CRUD usa `Route::resource('products', ProductController::class)` (7 rutas REST). Para APIs, `Route::apiResource`.
- Nombres REST generados: `products.index`, `products.create`, `products.store`, `products.show`, `products.edit`, `products.update`, `products.destroy`.
- **El orden importa**: Laravel usa la primera ruta que coincide. Las rutas fijas (`/usuario/crear`) van **antes** que las rutas con parámetro (`/usuario/{nombre}`).
- Restringe parámetros cuando convenga: `->whereNumber('id')`, `->whereAlpha('nombre')`, etc.
- Verbos: GET (consultar), POST (crear), PUT/PATCH (actualizar), DELETE (borrar). HTML solo admite GET y POST, así que en formularios se simulan con `@method('PUT')`, `@method('PATCH')` o `@method('DELETE')`.
- Rutas protegidas: middleware `auth`. Ejemplo ya aplicado en el proyecto:

```php
Route::resource('products', ProductController::class)->only(['index', 'show']);

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->except(['index', 'show']);
});
```

## Controladores

- Se crean con Artisan: `sail artisan make:controller ProductController --resource` (o `--api` para APIs).
- Heredan de `Controller`. Cada método termina con un `return`: una vista (`view(...)`), una redirección (`redirect()->route(...)`), JSON (`response()->json(...)`) o un string.
- Los datos de formularios se leen con el objeto `Request` inyectado (`$request->input('x')`, `$request->all()`), no con `$_GET`/`$_POST`.
- **Siempre valida en el servidor** con `$request->validate([...])` antes de guardar.
- Patrón habitual en este proyecto:

```php
public function store(Request $request)
{
    $request->validate([
        'name'  => 'required|max:100',
        'price' => 'required|numeric|min:0',
    ]);

    Product::create($request->all());

    return redirect()->route('products.index');
}
```

- Usa `findOrFail($id)` para que un id inexistente dé un 404.
- Pasar datos a la vista: `compact('productos')` o array `['productos' => $productos]` (las formas más usadas).
- Los controladores no llevan SQL: usan los modelos.

## Vistas Blade

- Todas las vistas **heredan del master layout**: `@extends('layouts.app')`, con `@section('title', '...')` y `@section('content') ... @endsection`.
- El layout (`layouts/app.blade.php`) define `@yield('title')`, `@yield('content')` y carga `@vite([...])`.
- Mostrar variables con `{{ $variable }}` (escapa HTML y protege de XSS). Valor por defecto: `{{ $x ?? 'texto' }}`. No uses `<?php ... ?>` ni `echo`.
- Condiciones y bucles con directivas: `@if/@else/@endif`, `@foreach/@endforeach`, `@forelse/@empty`, `@isset`, `@switch`. Dentro de un bucle, `$loop` da información de la iteración.
- Enlaces y formularios con `route('nombre', $parametro)`.
- Los datos de los modelos se acceden como objeto: `$producto->name` (no `$producto['name']`, que era solo para los arrays de prueba).
- Relaciones en vistas: `{{ $producto->category ? $producto->category->name : 'Sin categoría' }}` (la categoría puede ser nula).
- Evita `@php` salvo que sea imprescindible.

### Formularios

Todo formulario debe llevar:

1. `@csrf` (obligatorio en cualquier formulario que modifique datos).
2. `@method('PUT' | 'PATCH' | 'DELETE')` si no es GET/POST, con `method="POST"` en la etiqueta.
3. `old('campo')` en los valores para repoblar tras un error de validación.
4. `@error('campo') <span>{{ $message }}</span> @enderror` junto a cada campo.

```blade
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <input type="text" name="name" value="{{ old('name') }}">
    @error('name') <span>{{ $message }}</span> @enderror
    <button type="submit">Guardar producto</button>
</form>
```

- Valida también en el cliente (atributos HTML como `required`), pero **nunca como sustituto** de la validación del servidor.
- El borrado se hace con un formulario `POST` + `@method('DELETE')`, nunca con un enlace.

## Base de datos: migraciones y seeders

- Cambios de estructura **siempre por migración**, nunca a mano en MySQL.
- Crear tabla: `sail artisan make:migration create_<tabla>_table` (o `make:model Nombre -m`).
- Modificar una tabla existente: **nueva migración** con `--table=<tabla>`; no edites una migración ya ejecutada (salvo en desarrollo y avisándome, porque implica reconstruir la tabla y perder datos).
- Toda migración necesita `up()` y un `down()` que la revierta correctamente.
- Tipos habituales: `string('name', 100)`, `text('description')->nullable()`, `decimal('price', 8, 2)`, `integer`, `date`, `timestamps()`.
- Claves foráneas: `$table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');`. Usa `onDelete('cascade')` con cuidado.
- Seeders: `sail artisan make:seeder NombreSeeder` (en singular), y regístralos con `$this->call([...])` en `DatabaseSeeder.php`.

## Eloquent (modelos)

- Crear modelo: `sail artisan make:model Product` (añade `-m` si también hace falta migración).
- Define **`$fillable`** con los campos permitidos para asignación masiva (`create()`, `update()`, `fill()`):

```php
class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

- Métodos habituales: `all()`, `find()`, `findOrFail()`, `where()->get()`, `first()`, `orderBy()`, `create()`, `update()`, `save()`, `delete()`, `count()`, `max()`.
- Relaciones (se definen en **ambos** modelos):
  - 1:1 → `hasOne` / `belongsTo`
  - 1:N → `hasMany` / `belongsTo` (ejemplo del proyecto: `Category` tiene muchos `Product`)
  - N:N → `belongsToMany` en ambos lados; en la tabla pivote se usa `attach()`, `sync()` y `detach()`
- Se accede a una relación como atributo (`$user->articulos`) o como consulta extensible (`$user->articulos()->where(...)->first()`).
- Usa QueryBuilder (`DB::table(...)`) solo para informes o consultas que no encajen en los modelos. SQL crudo solo si no hay otra opción, **siempre con parámetros** (`?`) para evitar inyección SQL.

## Sesiones y autenticación

- Autenticación con **Laravel Breeze** (Blade). Rutas en `routes/auth.php`, vistas en `resources/views/auth/`. No reescribas el login a mano.
- Autenticación = quién eres; autorización = qué puedes hacer.
- En vistas: `@auth ... @endauth` y `@guest ... @endguest`. Usuario actual: `Auth::user()` (o `Auth::check()`).
- El logout de Breeze es **POST** (formulario con `@csrf`), no un enlace.
- Variables flash (duran una petición): `->with('mensaje', '...')` en la redirección y `session('mensaje')` en la vista.
- Los botones de crear/editar/borrar se ocultan a invitados con `@auth`, **y además** las rutas se protegen con el middleware `auth` (ocultar el botón no basta).

## Reglas de oro

- No inventes rutas, métodos ni clases de Laravel: si dudas, consulta la documentación oficial (https://laravel.com/docs) o pregúntame.
- No edites `vendor/`, `storage/` ni `bootstrap/`.
- No añadas paquetes de Composer/npm sin preguntarme.
- Tras cambiar rutas, comprueba con `sail artisan route:list` (y `route:clear` si no aparecen).
- Tras cambiar CSS/JS, recuerda `sail npm run dev` (desarrollo) o `sail npm run build` (versión final).
- No ejecutes comandos destructivos sobre la BD sin confirmación.
- Mantén el código limpio y comentado de forma breve, en español, siguiendo el estilo de las prácticas.