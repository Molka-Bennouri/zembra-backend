<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth('api')->id())
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function markSeen(Notification $notification): JsonResponse
    {
        $this->authorizeNotification($notification);
        $notification->update(['seen' => true]);
        return response()->json($notification);
    }

    public function markAllSeen(): JsonResponse
    {
        Notification::where('user_id', auth('api')->id())
            ->where('seen', false)
            ->update(['seen' => true]);

        return response()->json(['message' => 'All notifications marked as seen']);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        $this->authorizeNotification($notification);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }

    public function clearAll(): JsonResponse
    {
        Notification::where('user_id', auth('api')->id())->delete();
        return response()->json(['message' => 'All notifications cleared']);
    }

    private function authorizeNotification(Notification $notification): void
    {
        abort_if($notification->user_id !== auth('api')->id(), 403, 'Unauthorized');
    }
}
