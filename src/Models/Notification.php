<?php

namespace Evexel\FirebaseNotification\Models;

use Kreait\Firebase\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'data',
    ];

    public function source()
    {
        return $this->morphTo();
    }

    public function target()
    {
        return $this->morphTo();
    }

    public static function create_notification($type, $source, $target, $data)
    {
        return self::create([
            'type' => $type,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->id,
            'data' => json_encode($data),
        ]);
    }

    public function users()
    {
        return $this->belongsToMany(config('firebase-notification.user_model'), 'notification_user')->withPivot('read');
    }

    public function send_to_user($user_id)
    {
        $this->users()->attach([$user_id => ['read' => 0]]);
        $this->send([$user_id]);
    }

    public function send_to_users($users_ids)
    {
        $data = [];
        foreach ($users_ids as $user_id) {
            $data[$user_id] = ['read' => 0];
        }
        $this->users()->attach($data);
        $this->send($users_ids);
    }

    private function send($users_ids)
    {
        if (config('firebase-notification.credentials_path')) {
            $factory = (new Factory)->withServiceAccount(Storage::path(config('firebase-notification.credentials_path')));
            $factory->createAuth();
            $cloudMessaging = $factory->createMessaging();
            $supportedLanguages = config('firebase-notification.supported_languages', ['en']);

            $message = [];
            $registration_ids = [];
            $icon = $this->get_icon();
            foreach ($supportedLanguages as $lang) {
                $message[$lang] = $this->get_message($lang);
                $registration_ids[$lang] = [];
            }

            $fcm_tokens = config('firebase-notification.fcm_token_model')::whereIn('user_id', $users_ids)->get();
            foreach ($fcm_tokens as $fcm_token) {
                $registration_ids[$fcm_token->language][] = $fcm_token->fcm_token;
            }

            foreach ($supportedLanguages as $lang) {
                $android_config = AndroidConfig::fromArray([
                    'notification' => [
                        'title' => config('firebase-notification.app_name', 'App Name'),
                        'body' => $message[$lang],
                        'icon' => $icon,
                    ],
                ]);

                $ios_config = ApnsConfig::fromArray([
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => config('firebase-notification.app_name', 'App Name'),
                                'body' => $message[$lang],
                                'icon' => $icon,
                            ],
                            'badge' => 42,
                            'sound' => 'default',
                        ],
                    ],
                ]);

                if (count($registration_ids[$lang]) > 0) {
                    $cloudMessaging->sendMulticast(
                        CloudMessage::new()->withData([
                            'title' => config('firebase-notification.app_name', 'App Name'),
                            'body' => $message[$lang],
                            'icon' => $icon,
                        ])->withAndroidConfig($android_config)->withApnsConfig($ios_config),
                        $registration_ids[$lang]
                    );
                }
            }
        }
    }

    public function get_message($language)
    {
        $params = [];
        $data = json_decode($this->data, true);

        // Get message from translation file based on notification type
        return __("firebase-notification::notifications.{$this->type}", $params + ($data ?? []), $language);
    }

    public function get_icon()
    {
        return config('firebase-notification.app_icon', config('app.url') . '/imgs/logo.svg');
    }
}