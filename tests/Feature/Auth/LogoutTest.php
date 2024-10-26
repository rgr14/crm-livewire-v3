<?php

use App\Livewire\Auth\Logout;
use App\Models\User;
use Livewire\Livewire;


it('should be able to logout of the application', function () {
    $user = User::factory()->create();

    Livewire::test(Logout::class)
        ->call('logout')
        ->assertRedirect(route('login'));

    expect(auth())
        ->guest()
        ->toBeTrue();
});
