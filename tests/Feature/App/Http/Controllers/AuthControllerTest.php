<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Events\UserCreateEvent;
use App\Http\Controllers\AuthController;
use App\Listeners\SendEmailNewUserListener;
use App\Listeners\SendEmailRegisteredListener;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_login_page_success(): void
    {
        $this->get(action([AuthController::class, 'index']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.index');
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([AuthController::class, 'signUp']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /**
     * @test
     * @return void
     */
    public function it_forgot_page_success(): void
    {
        $this->get(action([AuthController::class, 'forgot']))
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

        $user = User::factory()->create([
            'email' => 'test@test.com',
        ]);

        $response = $this->post(action([AuthController::class, 'forgotPassword']), ['email' => $user->email]);

        $response->assertStatus(302);
    }

    /**
     * @test
     * @return void
     */
    public function it_reset_page_success(): void
    {
        $token = 'test-token';

        $response = $this->get(action([AuthController::class, 'reset'], ['token' => $token]));

        $response->assertOk()
               ->assertViewIs('auth.reset-password')
               ->assertViewHas('token', $token);
    }

    /**
     * @test
     * @return void
     */
    public function test_reset_password_success(): void
    {
        Event::fake();

        $email = 'test@test.com';
        $user = User::factory()->create(['email' => $email]);

        $token = Password::createToken($user);

        $request = [
            'email' => $email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => $token,
        ];

        // Проверяем, что пользователь еще не изменил пароль
        $this->assertDatabaseMissing('users', [
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        // Отправляем POST запрос на маршрут сброса пароля
        $response = $this->post(action([AuthController::class, 'resetPassword']), $request);

        // Проверяем, что статус ответа 302 (перенаправление)
        $response->assertStatus(302);

        // Извлекаем пользователя из базы данных
        $user = User::where('email', $request['email'])->first();

        // Проверяем, что пароль пользователя был изменен
        $this->assertTrue(Hash::check($request['password'], $user->password));

        // Проверяем, что событие PasswordReset было отправлено
        Event::assertDispatched(PasswordReset::class);

        // Проверяем, что пользователь был перенаправлен на страницу входа
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_in_success(): void
    {
        $password = '12345678';
        $user = User::factory()->create([
            'password' => bcrypt($password),
            'email' => 'test@test.com',
        ]);

        $request = ([
            'email' => $user->email,
            'password' => $password,
        ]);

        $response = $this->post(action([AuthController::class, 'signIn']), $request);

        $response
            ->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }


    /**
     * @test
     * @return void
     */
    public function it_store_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = [
            'name' => 'TestName',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

//        $data = SignUpFormRequestFactory::new()->fake();
//        dd($data);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email'],
        ]);

        $response = $this->post(
            action([AuthController::class, 'store']),
            $request,
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email'],
        ]);

        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailRegisteredListener::class);

        Event::assertDispatched(UserCreateEvent::class);
        Event::assertListening(UserCreateEvent::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailRegisteredListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function it_logout_success(): void
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
        ]);

        $this->actingAs($user)
            ->delete(action([AuthController::class, 'logOut']));

        $this->assertGuest();
    }
}
