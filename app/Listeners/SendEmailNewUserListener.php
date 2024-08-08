<?php

namespace App\Listeners;

use App\Events\UserCreateEvent;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailNewUserListener
{
    /**
     * Handle the event.
     */
    public function handle(UserCreateEvent $event): void
    {
        $event->user->notify(new NewUserNotification());
    }

}
