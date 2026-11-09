<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'due_date', 'status', 'user_id'];

    /**
     * Relación 1:N inversa - Un proyecto pertenece a un usuario creador
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación N:N - Un proyecto tiene muchos usuarios participantes
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role', 'contribution_hours')
                    ->withTimestamps();
    }
}
