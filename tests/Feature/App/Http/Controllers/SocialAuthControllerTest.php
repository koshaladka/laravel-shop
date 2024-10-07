<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery\MockInterface;
use Tests\TestCase;


class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

   private function mockSocialiteCallBack(string|int $githubId): MockInterface
   {
       $user = $this->mock(SocialiteUser::class, function (MockInterface $m) use ($githubId) {
           $m->shouldReceive('getId')
               ->once()
               ->andReturn($githubId);

           $m->shouldReceive('getName')
               ->once()
               ->andReturn(str()->random(10));

           $m->shouldReceive('getEmail')
               ->once()
               ->andReturn('test@test.com');
       });

       Socialite::shouldReceive('driver->user')
           ->once()
           ->andReturn($user);

       return $user;
   }

   private function callbackRequest(): TestResponse
   {
       return $this->get(
           action(
               [SocialAuthController::class, 'githubCallback'],
               ['driver' => 'github']
           )
       );
   }

    /**
     * @test
     * @return void
     */
    public function it_github_callback_created_user_success(): void
    {
        $githubId = str()->random(10);

        $this->assertDatabaseMissing('users', [
            'github_id' => $githubId,
        ]);

        $this->mockSocialiteCallBack($githubId);

        $this->callbackRequest()
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'github_id' => $githubId,
        ]);
    }

}
