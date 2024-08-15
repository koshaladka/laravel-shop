<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class SignUpFormRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
           'email' => $this->faker->email,
            'password' => $this->faker->password,
            'name' => $this->faker->name,
        ];
    }
}
