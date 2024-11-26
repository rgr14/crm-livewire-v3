<?php

use App\Livewire\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('should be able to access the route admin/users', function () {

     actingAs(User::factory()->admin()->create());


    get(route('admin.users'))
        ->assertOk();
});

test('making sure that the route is protected by the permission BE_AN_ADMIN', function () {
   actingAs(User::factory()->create());

    get(route('admin.users'))
        ->assertForbidden();
});

test("let's create a livewire component to list all users in the page", function () {
    actingAs(User::factory()->admin()->create());
    $users = User::factory()->count(10)->create();

    $lw = Livewire::test(Admin\Users\Index::class)
        ->assertSet('users', function ($users) {
            expect($users)
                ->toBeInstanceOf(Collection::class)
                ->toHaveCount(11);

            return true;
        });

    foreach ($users as $user) {
        $lw->assertSee($user->name);
    }

});

test('check the table format', function () {
    actingAs(User::factory()->admin()->create());

    Livewire::test(Admin\Users\Index::class)
        ->assertSet('headers', [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'permissions', 'label' => 'Permissions']
        ]);
});

test('should be able to filter by name and email', function () {
    $admin = User::factory()->admin()->create(['name' => 'Joe Doe', 'email' => 'admin@example.com']);
    $mario = User::factory()->create(['name' => 'Mario', 'email' => 'little_guy@example.com']);

    actingAs($admin);

    Livewire::test(Admin\Users\Index::class)
        ->assertSet('users', function ($users) {
            expect($users)->toHaveCount(2);

            return true;
        })
        ->set('search', 'mar')
        ->assertSet('users', function ($users) {
            expect($users)
                ->toHaveCount(1)
                ->first()->name->toBe('Mario');

            return true;
        })
        ->set('search', 'guy')
        ->assertSet('users', function ($users) {
            expect($users)
                ->toHaveCount(1)
                ->first()->name->toBe('Mario');

            return true;
        });
});
