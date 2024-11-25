<?php

it('should be able to access the route admin/users', function () {

    $user = \App\Models\User::factory()->admin()->create();


    \Pest\Laravel\get(route('admin.users'))
        ->assertOk();
});
