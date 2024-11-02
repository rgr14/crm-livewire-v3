<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

it('should block the access to an admin page if the user dos not have the permission to be an admin', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test("let's make sure that we are using cache to store user permissions", function () {
    $user = User::factory()->create();

    $user->givePermissionTo('be an admin');

    $cachekey = "user::{$user->id}::permissions";

    expect(Cache::has($cachekey))->toBeTrue('Checking if cache key exists')
        ->and(Cache::get($cachekey))->toBe($user->permissions, 'Checking if permissions are the same as the user');
});

test("le'ts make sure that we are using the cache the retrieve/check when the user has the giver permission", function () {
    $user = User::factory()->create();

    $user->givePermissionTo('be an admin');

    DB::listen( fn ($query) => throw new Exception('We got a hit'));
    $user->hasPermissionTo('be an admin');

    expect(true)->toBeTrue();
});


