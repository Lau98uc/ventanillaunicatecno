<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{

    public function scopeOrderByName($query)
    {
        return $query->orderBy('name');
    }


    public function scopeFilter($query, array $filters)
    {
        // Si viene filtro de búsqueda
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // Puedes agregar más filtros aquí si necesitas, ejemplo:
        // if (!empty($filters['status'])) { ... }
    }
}
