<?php

namespace App\Listeners;

use App\Events\UserCreateEvent;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailRegisteredListener
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $event->user->notify(new NewUserNotification());
    }

}
