<?php

namespace App\Listeners;

use App\Events\NewMessageEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyMessageCorrespondantListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewMessageEvent  $event
     * @return void
     */
    public function handle(NewMessageEvent $event)
    {

        $message = $event->message;
        $thread = $message->thread;


        if ($message->is_from_admin) {


            Notification::create([
                "title" => "New message from Admin",
                "content" => $message->text,
                "user_id" => $thread->user_id,
            ]);
        } else {

            $admins = User::where("role", "admin")->get(["id"]);

            $notificationsToAllAdmins = collect($admins)
                ->map(function (User $user) use ($message) {

                    return [
                        "title" => "New claim  from a User",
                        "content" => $message->text,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ];
                });

            Notification::insert($notificationsToAllAdmins->toArray());
        }
    }
}
