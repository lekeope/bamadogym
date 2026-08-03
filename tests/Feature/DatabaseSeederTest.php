<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_admin_and_plans_without_faker(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@bamadogym.com')->first();

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertSame(3, Plan::query()->count());
    }

    public function test_seeder_is_idempotent_and_keeps_existing_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@bamadogym.com')->firstOrFail();
        $admin->update(['password' => 'already-changed']);

        $this->seed(DatabaseSeeder::class);

        $admin->refresh();

        $this->assertTrue(Hash::check('already-changed', $admin->password));
        $this->assertSame(3, Plan::query()->count());
        $this->assertSame(1, User::query()->where('email', 'admin@bamadogym.com')->count());
    }
}
