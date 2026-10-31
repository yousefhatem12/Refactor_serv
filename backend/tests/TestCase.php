<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist for user boot observer and contact notification queries
        if (class_exists(Role::class) && Schema::hasTable('roles')) {
            Role::findOrCreate('read_only');
            Role::findOrCreate('super_admin');
        }
    }
}
