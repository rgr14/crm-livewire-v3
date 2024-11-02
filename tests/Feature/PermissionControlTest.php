<?php

use App\Models\Permission;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('should be able to give an user a permission to do something', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $user->givePermissionTo('be an admin');

    expect($user)
        ->hasPermissionTo('be an admin')
        ->toBeTrue();

    \Pest\Laravel\assertDatabaseHas('permissions', [
        'key' => 'be an admin',
    ]);

    \Pest\Laravel\assertDatabaseHas('permission_user', [
        'user_id' => $user->id,
        'permission_id' => Permission::where('key', '=', 'be an admin')->first()->id,
    ]);
});

test('permissions has to have a seeder', function () {
   \Pest\Laravel\seed(\Database\Seeders\PermissionSeeder::class);

   assertDatabaseHas('permissions', [
       'key' => 'be an admin',
   ]);
});

test('seed with an admin user', function () {
    \Pest\Laravel\seed([
        \Database\Seeders\PermissionSeeder::class,
        \Database\Seeders\UserSeeder::class
    ]);

    assertDatabaseHas('permissions', [
        'key' => 'be an admin',
    ]);

    assertDatabaseHas('permission_user', [
        'user_id' => User::first()?->id,
        'permission_id' => Permission::where(['key' => 'be an admin'])->first()?->id,
    ]);
});

test('should block the access to an admin page if the user dos not have the permission to be an admin', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
