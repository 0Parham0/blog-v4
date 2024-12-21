<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;
        return NotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return $this->ok('Notification marked as read');
    }
}
