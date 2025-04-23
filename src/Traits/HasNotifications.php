<?php

namespace ZeinApi\FirebaseNotification\Traits;

use ZeinApi\FirebaseNotification\Models\Notification;

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