<?php

use App\Livewire\Auth\Register;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Livewire\Livewire;
use function Pest\Laravel\assertDatabaseHas;
use function \Pest\Laravel\assertDatabaseCount;
use App\Models\User;

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
        ->assertHasNoErrors()
        ->assertRedirect('/');
    ;

    assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ]);

    assertDatabaseCount('users', 1);


    expect(auth()->check())
        ->and(auth()->user())
        ->id->toBe(User::first()->id);
});

test('validation rules', function ($f) {
    Livewire::test(Register::class)
        ->set($f->field, $f->value)
        ->call('submit')
        ->assertHasErrors([$f->field => $f->rule]);
})->with([
    'name::required' => (object)['field' => 'name', 'value' => '', 'rule' => 'required'],
    'name::max:255' => (object)['field' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max'],
    'email::required' => (object)['field' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email' => (object)['field' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::max:255' => (object)['field' => 'email', 'value' => str_repeat('*' . '@doe.com', 256), 'rule' => 'max'],
    'email::confirmed' => (object)['field' => 'email', 'value' => 'joe@doe.com', 'rule' => 'confirmed'],
    'password::required' => (object)['field' => 'password', 'value' => '', 'rule' => 'required'],
]);

