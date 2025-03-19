<?php
// filepath: d:\studies\CharityBridge\app\Http\Controllers\NotificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->notifications();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('data->type', $request->type);
        }

        // Filter by read/unread status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } else if ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        // Paginate the results
        $notifications = $query->paginate(10);

        // Count unread notifications
        $unreadCount = $request->user()->unreadNotifications()->count();

        // Get user role
        $userRole = $request->user()->role;

        return Inertia::render('Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'userRole' => $userRole,
            'filters' => [
                'type' => $request->type ?? 'all',
                'status' => $request->status ?? 'all',
            ],
        ]);
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->delete();
        }

        return redirect()->back();
    }
}
