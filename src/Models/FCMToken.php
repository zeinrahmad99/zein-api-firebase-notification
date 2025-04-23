<?php

namespace Evexel\FirebaseNotification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FCMToken extends Model
{
    use HasFactory;

    protected $table = 'fcm_tokens';

    protected $fillable = [
        'user_id',
        'device_token',
        'fcm_token',
        'language',
    ];

    public function user()
    {
        return $this->belongsTo(config('firebase-notification.user_model'));
    }
}