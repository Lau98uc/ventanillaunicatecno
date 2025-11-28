<?php

namespace App\Traits;

use App\Models\Notification;

trait NotifiesUsers
{
    /**
     * Notificar a un solo usuario
     */
    public function notifyUser($userId, $title, $message, $type = null, $link = null, $image = null, $notifiable = null)
    {
        Notification::create([
            'user_id'         => $userId,
            'title'           => $title,
            'message'         => $message,
            'type'            => $type,
            'link'            => $link,
            'image'           => $image,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'   => $notifiable?->id,
        ]);
    }

    /**
     * Notificar a múltiples usuarios
     */
    public function notifyUsers(array $userIds, $title, $message, $type = null, $link = null, $image = null, $notifiable = null)
    {
        foreach ($userIds as $userId) {
            $this->notifyUser($userId, $title, $message, $type, $link, $image, $notifiable);
        }
    }
}
