<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMember;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function sendEventReminder()
    {
        $time = Carbon::now('UTC')->addMinutes(10)->format('Y-m-d H:i:00');
        $events = Event::where('start', $time)->get();
        foreach ($events as $event) {
            $members = EventMember::where('event_id', $event->id)->pluck('user_id')->toArray();
            $notificationData = [
                'receiver_id' => $members,
                'title' => ['Event'],
                'body' => ['EVENT_REMINDER', ['name' => $event->title, 'time' => Carbon::parse($event->start)->format('H:i')]],
                'type' => 'event_reminder',
                'app_notification_data' => ['EVENT_REMINDER', ['name' => $event->title, 'time' => Carbon::parse($event->start)->format('H:i')]]
            ];
            Helper::Notification($notificationData, true, true, $event->user_id);
        }
    }
}
