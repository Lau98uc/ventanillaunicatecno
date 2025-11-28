<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'label',
        'slug',
        'ruta',
        'icon',
        'type',
        'parent_id',
        'orden',
        'visible',
    ];

    // Relaciones

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    // Scopes opcionales

    public function scopeModelo($query)
    {
        return $query->where('type', 'modelo');
    }

    public function scopeAcciones($query)
    {
        return $query->where('type', 'accion');
    }

    public function scopeModulos($query)
    {
        return $query->where('type', 'modulo');
    }

    public function scopeVisibles($query)
    {
        return $query->where('visible', true);
    }
}
