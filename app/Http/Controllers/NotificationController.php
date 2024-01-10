<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(5);
        return view('frontend.notifications', compact('notifications'));
    }

    public function show($id) {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        return view('frontend.notification_detail', compact('notification'));
    }

    public function destory($id) {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->delete();
        return "success";
    }

}
