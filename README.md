<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="360" alt="Logo de Laravel">
    </a>
</p>

# Gestor de proyectos

## Conversacion con la IA
<p>
   <a href="https://claude.ai/code" target="_blank">
        <img src="https://cdn.simpleicons.org/claude/D97757" width="72" alt="Logo de Claude Code">
   </a>
</p>

La conversación completa está en [CONVERSACION_IA.md](./CONVERSACION_IA.md)

## Sobre el proyecto

Aplicación web de prácticas de Laravel para gestionar proyectos y sus participantes. Los usuarios registrados pueden crear proyectos con descripción, fecha de vencimiento y estado; cada proyecto tiene un usuario creador y puede incluir a otros usuarios como participantes. El creador puede gestionar esos participantes y sus horas de contribución.

## Funcionalidades

- Registro, inicio y cierre de sesión, y gestión del perfil con Laravel Breeze.
- Operaciones CRUD para proyectos: crear, consultar, editar y eliminar.
- Listado de proyectos ordenado por fecha de vencimiento.
- Relación de cada proyecto con su creador.
- Gestión de participantes: añadirlos con un rol, actualizar sus horas de contribución y quitarlos.
- Acciones de edición y administración reservadas al creador del proyecto.

## Tecnologías

- PHP 8.3 o superior y Laravel 13.
- Laravel Sail y Docker para ejecutar la aplicación y sus servicios.
- MySQL 8.4 como base de datos.
- Laravel Breeze con vistas Blade para autenticación.
- Vite, Tailwind CSS y Alpine.js para los recursos del frontend.

## Requisitos

- Docker Desktop o Docker Engine con Docker Compose.
- Dependencias PHP del proyecto instaladas para disponer de `vendor/bin/sail`.

No es necesario instalar PHP, Composer, Node.js ni MySQL directamente en el equipo: los comandos de desarrollo se ejecutan mediante Sail.

## Puesta en marcha

1. Crea el archivo local de entorno a partir del ejemplo:

   ```bash
   cp .env.example .env
   ```

2. En `.env`, configura la conexión de base de datos de Sail:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=sail
   DB_PASSWORD=password
   ```

3. Levanta los contenedores:

   ```bash
   ./vendor/bin/sail up -d
   ```

4. Instala las dependencias, genera la clave de la aplicación y ejecuta las migraciones:

   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate
   ```

5. Instala las dependencias de frontend y arranca Vite:

   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

La aplicación queda disponible en [http://localhost](http://localhost). Para detener los contenedores, ejecuta `./vendor/bin/sail down`.

> Las migraciones normales conservan los datos existentes. No uses `migrate:fresh` salvo que quieras borrar todas las tablas y volver a crearlas.

## Modelo de datos

- **`users`**: usuarios registrados; sirve tanto para identificar quién crea un proyecto como quién participa en él.
- **`projects`**: título, descripción, fecha de vencimiento, estado y `user_id` del creador.
- **`project_user`**: tabla pivote de participantes, con `project_id`, `user_id`, `role` y `contribution_hours`.

Las relaciones Eloquent son uno a muchos entre usuario y proyectos creados, y muchos a muchos entre usuarios y proyectos participantes. La relación muchos a muchos incluye los campos adicionales de la tabla pivote.

## Estructura principal

```text
app/
├── Http/Controllers/   # Controladores de proyectos y perfil
└── Models/             # Modelos Project y User
database/migrations/    # Estructura de users, projects y project_user
resources/views/
├── projects/           # Vistas Blade del CRUD y participantes
└── auth/               # Vistas de autenticación de Breeze
routes/
├── web.php             # Rutas web y recursos de proyectos
└── auth.php            # Rutas de autenticación
CONVERSACION_IA.md      # Historial de trabajo con la IA
```