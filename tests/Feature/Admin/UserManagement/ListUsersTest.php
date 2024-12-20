<?php

use App\Livewire\Admin;
use App\Models\Permission;
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

it('should be able to filter by name and email', function () {
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

it('should be able to filter by permission.key', function () {
    $admin = User::factory()->admin()->create(['name' => 'Joe Doe', 'email' => 'admin@example.com']);
    $mario = User::factory()->create(['name' => 'Mario', 'email' => 'little_guy@example.com']);

    $permission = Permission::where('key', '=', \App\Enum\Can::BE_AN_ADMIN)->first();

    actingAs($admin);

    Livewire::test(Admin\Users\Index::class)
        ->assertSet('users', function ($users) {
            expect($users)->toHaveCount(2);

            return true;
        })
        ->set('search_permissions', [$permission->id])
        ->assertSet('users', function ($users) {
            expect($users)
                ->toHaveCount(1)
                ->first()->name->toBe('Joe Doe');

            return true;
        });
});

it('should be able to list deleted users', function () {
    $admin = User::factory()->admin()->create(['name' => 'Joe Doe', 'email' => 'admin@example.com']);
    $mario = User::factory()->count(2)->create(['deleted_at' => now()]);

    $permission = Permission::where('key', '=', \App\Enum\Can::BE_AN_ADMIN)->first();

    actingAs($admin);

    Livewire::test(Admin\Users\Index::class)
        ->assertSet('users', function ($users) {
            expect($users)->toHaveCount(1);

            return true;
        })
        ->set('search_trash', true)
        ->assertSet('users', function ($users) {
            expect($users)
                ->toHaveCount(2);

            return true;
        });
});

it('should be able to sort by name', function () {
    $admin = User::factory()->admin()->create(['name' => 'Joe Doe', 'email' => 'admin@example.com']);
    $nonADmin = User::factory()->withPermission(\App\Enum\Can::TESTING)->create(['name' => 'Mario', 'email' => 'lillet_guy@example.com']);

    // ASC => Joe, Mario
    // DESC => Mario, Joe
    actingAs($admin);
    Livewire::test(Admin\Users\Index::class)
        ->set('sortDirection', 'asc')
        ->set('sortColumnBy', 'name')
        ->assertSet('users', function ($users) {
            expect($users)
                ->first()->name->toBe('Joe Doe')
                ->and($users)->last()->name->toBe('Mario');
            return true;
        })
        ->set('sortDirection', 'desc')
        ->set('sortColumnBy', 'name')
        ->assertSet('users', function ($users) {
            expect($users)
                ->first()->name->toBe('Mario')
                ->and($users)->last()->name->toBe('Joe Doe');
            return true;
        })
    ;
});
