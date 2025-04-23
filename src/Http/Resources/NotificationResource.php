<?php

namespace Evexel\FirebaseNotification\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();
        Carbon::setLocale($currentLocale);
        $date = "";
        $diff_days = $this->created_at->diff()->days;

        if ($diff_days <= 3) {
            $date = $this->created_at->diffForHumans();
        } else {
            if ($this->created_at->format('Y') == Carbon::now()->format('Y')) {
                $date = $this->created_at->format('d M');
            } else {
                $date = $this->created_at->format('d M Y');
            }
        }

        return [
            'id' => $this->id,
            'icon' => $this->get_icon(),
            'type' => __("firebase-notification::notifications.{$this->type}", [], $currentLocale),
            'message' => $this->resource->get_message($currentLocale),
            'data' => json_decode($this->data),
            'read' => $this->pivot->read,
            'date' => $date,
        ];
    }
}