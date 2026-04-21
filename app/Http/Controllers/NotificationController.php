<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::where('client_id', auth('clients')->id())
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
        Notification::where('client_id', auth('clients')->id())
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
        Notification::where('client_id', auth('clients')->id())->delete();
        return response()->json(['message' => 'All notifications cleared']);
    }

    private function authorizeNotification(Notification $notification): void
    {
        abort_if($notification->client_id !== auth('clients')->id(), 403, 'Unauthorized');
    }
}
