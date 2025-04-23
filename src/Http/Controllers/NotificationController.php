<?php

namespace ZeinApi\FirebaseNotification\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use ZeinApi\FirebaseNotification\Models\FCMToken;
use ZeinApi\FirebaseNotification\Models\Notification;
use ZeinApi\FirebaseNotification\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    public function register_token(Request $request)
    {
        $request->validate([
            'device_token' => ['required', 'string'],
            'fcm_token' => ['required', 'string'],
            'language' => ['required', Rule::in(config('firebase-notification.supported_languages', ['en']))],
        ]);

        $user = Auth::user();

        FCMToken::updateOrCreate(
            ['user_id' => $user->id, 'device_token' => $request->device_token],
            ['fcm_token' => $request->fcm_token, 'language' => $request->language]
        );

        return response()->json(['message' => 'Token registered successfully']);
    }

    public function get_notifications(Request $request)
    {
        $request->validate([
            'per_page' => ['integer', 'min:1'],
        ]);

        $user = Auth::user();

        $query = $user->notifications()->orderBy('created_at', 'DESC');

        $notifications = $query->paginate($request->per_page ?? 10);
        $unread_notifications_count = $query->where('read', false)->count();

        return (NotificationResource::collection($notifications))
            ->additional([
                'meta' => [
                    'unread_notifications_count' => $unread_notifications_count,
                ]
            ]);
    }

    public function notifications_read(Request $request)
    {
        $request->validate([
            'notifications_ids' => ['required', 'array'],
            'notifications_ids.*' => ['integer'],
        ]);

        $user = Auth::user();

        $user->notifications()
            ->wherePivotIn('notification_id', $request->notifications_ids)
            ->updateExistingPivot($request->notifications_ids, ['read' => true]);

        return response()->json(['message' => 'Notifications marked as read']);
    }

    public function notifications_read_all(Request $request)
    {
        $user = Auth::user();

        $user->notifications()->updateExistingPivot(
            $user->notifications()->pluck('notifications.id'),
            ['read' => true]
        );

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function notifications_test(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'message' => ['required', 'string', 'max:50'],
        ]);

        $source = Auth::user();
        $target = config('firebase-notification.user_model')::find($request->user_id);

        $notification = Notification::create_notification(
            'test',
            $source,
            $target,
            [
                'message' => $request->message,
            ]
        );

        $notification->send_to_user($target->id);

        return response()->json(['message' => 'Test notification sent successfully']);
    }
}