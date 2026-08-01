<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AppSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_settings(): void
    {
        $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));
    }

    public function test_staff_cannot_view_settings(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_can_view_and_update_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Gym Settings');

        $payload = [
            'app_name' => 'Test Gym',
            'tagline' => 'Lift. Rest. Repeat.',
            'hero_subtitle' => 'A great place to train.',
            'about_heading' => 'About us',
            'about_blurb' => 'We train hard.',
            'contact_address' => '1 Test Street',
            'contact_phone' => '+234 111 111 1111',
            'contact_email' => 'hello@testgym.com',
            'hours_weekday' => '6:00 AM – 9:00 PM',
            'hours_saturday' => '7:00 AM – 7:00 PM',
            'hours_sunday' => '8:00 AM – 2:00 PM',
            'hours_holiday' => 'Closed',
            'mail_from_address' => 'noreply@testgym.com',
            'mail_from_name' => 'Test Gym',
            'currency' => 'ngn',
            'currency_locale' => 'en_NG',
            'reminder_due_soon_days' => '5',
            'reminder_overdue_days_1' => '2',
            'reminder_overdue_days_2' => '10',
        ];

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('Test Gym', AppSettings::get('app_name'));
        $this->assertSame('noreply@testgym.com', config('mail.from.address'));
        $this->assertSame('ngn', config('cashier.currency'));
    }
}
