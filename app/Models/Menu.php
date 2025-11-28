<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;



class Menu extends Model
{
    protected $table = 'permissions';

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

    protected static function booted()
    {
        static::addGlobalScope('onlyMenus', function (Builder $query) {
            $query->whereIn('type', ['menu', 'modulo']);
        });
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('orden');
    }

    public function permiso()
    {
        return $this->hasOne(Permission::class, 'name', 'name')->where('type', 'accion');
    }


}
