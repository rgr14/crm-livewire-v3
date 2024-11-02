<?php

use App\Models\Permission;
use App\Models\User;

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
   $this->seed(\Database\Seeders\PermissionSeeder::class);

   \Pest\Laravel\assertDatabaseHas('permissions', [
       'key' => 'be an admin',
   ]);
});


