# ZeinApi Firebase Notification

A powerful Firebase notification system created by Zein Rawad Ahmad.

## Installation

Add the package to your Laravel project:
```bash
composer require zein-api/firebase-notification
```

Publish the configuration file:
```bash
php artisan vendor:publish --provider="ZeinApi\FirebaseNotification\FirebaseNotificationServiceProvider"
```

Run the migrations:
```bash
php artisan migrate
```

## Configuration

### 1. Add Service Provider

Add the service provider to your `config/app.php`:
```php
'providers' => [
    // ...
    ZeinApi\FirebaseNotification\FirebaseNotificationServiceProvider::class,
],
```

### 2. Configure Environment Variables

Add these variables to your `.env` file:
```
FIREBASE_CREDENTIALS=firebase/your-credentials.json
APP_NAME=Your App Name
APP_URL=https://your-app.com
```

### 3. Add Trait to User Model

Add the HasNotifications trait to your User model:
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use ZeinApi\FirebaseNotification\Traits\HasNotifications;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasNotifications;
    
    // ... rest of your User model code
}
```

### 4. Configure Package Settings

The package configuration file (`config/firebase-notification.php`) allows you to customize:

- Firebase credentials path
- Application name and icon
- Supported languages
- User model class
- FCM token model class

## Usage

### Register FCM Token

When a user logs in from a device, register their FCM token:

```http
POST /api/fcm_token
{
    "device_token": "device-unique-id",
    "fcm_token": "firebase-cloud-messaging-token",
    "language": "en"
}
```

### Send Notification

```php
use ZeinApi\FirebaseNotification\Models\Notification;

// Create and send notification to a single user
$notification = Notification::create_notification(
    'notification_type',
    $source,  // The model that triggered the notification (optional)
    $target,  // The model that the notification is about (optional)
    [
        'key' => 'value',  // Additional data
    ]
);
$notification->send_to_user($user_id);

// Send to multiple users
$notification->send_to_users([$user_id1, $user_id2]);
```

### Get User's Notifications

```http
GET /api/notifications?per_page=10
```

### Mark Notifications as Read

```http
// Mark specific notifications as read
POST /api/notifications/read
{
    "notifications_ids": [1, 2, 3]
}

// Mark all notifications as read
POST /api/notifications/read/all
```

### Test Notification

```http
POST /api/notifications/test
{
    "user_id": 1,
    "message": "Test notification message"
}
```

## Translation

Add your notification messages to your translation files:

```php
// resources/lang/en/firebase-notification.php
return [
    'notification_type' => 'Your notification message with :parameter',
];
```

## Troubleshooting

If you get the error "Use of unknown class: 'ZeinApi\FirebaseNotification\Traits\HasNotifications'", make sure:

1. The package is properly installed:
```bash
composer require zein-api/firebase-notification
```

2. The service provider is registered in `config/app.php`:
```php
'providers' => [
    // ...
    ZeinApi\FirebaseNotification\FirebaseNotificationServiceProvider::class,
],
```

3. Run composer dump-autoload:
```bash
composer dump-autoload
```

4. Clear Laravel's configuration cache:
```bash
php artisan config:clear
php artisan cache:clear
```

## Author

Zein Rawad Ahmad
Email: zeinrahmad99@gmail.com 