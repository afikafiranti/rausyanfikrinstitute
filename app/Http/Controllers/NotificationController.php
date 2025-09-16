<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();
        $unread = $user->unreadNotifications()->count();
        return view('notifications.index', compact('notifications','unread'));
    }

    public function markAsRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id',$id)->firstOrFail();
        if (is_null($n->read_at)) $n->markAsRead();
        return back()->with('success','Notifikasi ditandai terbaca.');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success','Semua notifikasi ditandai terbaca.');
    }
}
