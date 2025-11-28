<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'notifiable_id',
        'notifiable_type',
        'link',
        'image',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Receptor
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Modelo relacionado (polimórfico)
    public function notifiable()
    {
        return $this->morphTo();
    }
}
