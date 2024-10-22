<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;
use function Pest\Laravel\assertDatabaseHas;
use function \Pest\Laravel\assertDatabaseCount;

it('renders successfully', function () {
    Livewire::test(Register::class)
        ->assertStatus(200);
});

it ('should be able to register a new user in the system', function () {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@doe.com')
        ->set('email_confirmation', 'john@doe.com')
        ->set('password', 'password')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ]);

    assertDatabaseCount('users', 1);
});
