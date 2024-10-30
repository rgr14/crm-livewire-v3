<?php

use App\Livewire\Auth\Password;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use function Pest\Laravel\get;

test('need to receive a valid token with a combination with the email', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test(Password\Recovery::class)
        ->set('email', $user->email)
        ->call('startPasswordRecovery');

    $token = DB::table('password_reset_tokens')
        ->where('email', '=', $user->email)
        ->first();

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) {
            get(route('password.reset') . '?token=' . $notification->token)
                ->assertSuccessful();

            get(route('password.reset') . '?token=' . 'any-token')
                ->assertRedirect(route('login'));

            return true;
        }
    );
});

