<?php

namespace Tests\Feature\App\Http\Controllers;


use App\Http\Controllers\Auth\ForgotPasswordController;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_forgot_password_success(): void
    {
        Notification::fake();

        $user = UserFactory::new()->create([
            'email' => 'test@test.com',
        ]);

        $response = $this->post(action([ForgotPasswordController::class, 'handle']), ['email' => $user->email]);

        $response->assertStatus(302);
    }

}
