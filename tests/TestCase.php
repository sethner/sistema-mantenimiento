<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function userWithRole(string $roleName): \App\Models\User
    {
        $role = \App\Models\Role::firstOrCreate(['nombre' => $roleName]);
        $user = \App\Models\User::factory()->create();
        $user->roles()->attach($role->id);

        return $user;
    }
}
