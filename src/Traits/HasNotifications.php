<?php

namespace Evexel\FirebaseNotification\Traits;

use Evexel\FirebaseNotification\Models\Notification;

trait HasNotifications
{
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
            ->withPivot('read')
            ->withTimestamps();
    }

    public function fcmTokens()
    {
        return $this->hasMany(config('firebase-notification.fcm_token_model'));
    }
}