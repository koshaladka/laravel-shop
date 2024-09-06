<?php

namespace Domain\Auth\Actions;

use App\Events\UserCreateEvent;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterNewUserAction implements RegisterNewUserContract
{
    public function __invoke(string $name, string $email, string $password)
    {
        try {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
            ]);

            event(new UserCreateEvent($user));
            event(new Registered($user));

            auth()->login($user);
        } catch (\Throwable $th) {

        }
    }

}
