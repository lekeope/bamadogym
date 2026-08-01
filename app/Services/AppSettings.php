<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppSettings
{
    public const CACHE_KEY = 'app.settings';

    /**
     * Keys editable in the admin panel, grouped for the UI.
     *
     * @return array<string, array{label: string, fields: array<string, array{label: string, type: string, help?: string, rows?: int}>>>
     */
    public static function schema(): array
    {
        return [
            'branding' => [
                'label' => 'Branding',
                'fields' => [
                    'app_name' => ['label' => 'Gym name', 'type' => 'text'],
                    'tagline' => ['label' => 'Tagline', 'type' => 'text', 'help' => 'Shown in page titles and the hero headline.'],
                    'hero_subtitle' => ['label' => 'Hero subtitle', 'type' => 'textarea', 'rows' => 3],
                    'about_heading' => ['label' => 'About heading', 'type' => 'text'],
                    'about_blurb' => ['label' => 'About blurb', 'type' => 'textarea', 'rows' => 2],
                ],
            ],
            'contact' => [
                'label' => 'Contact',
                'fields' => [
                    'contact_address' => ['label' => 'Address', 'type' => 'text'],
                    'contact_phone' => ['label' => 'Phone', 'type' => 'text'],
                    'contact_email' => ['label' => 'Public email', 'type' => 'email'],
                ],
            ],
            'hours' => [
                'label' => 'Opening hours',
                'fields' => [
                    'hours_weekday' => ['label' => 'Monday – Friday', 'type' => 'text'],
                    'hours_saturday' => ['label' => 'Saturday', 'type' => 'text'],
                    'hours_sunday' => ['label' => 'Sunday', 'type' => 'text'],
                    'hours_holiday' => ['label' => 'Public holidays', 'type' => 'text'],
                ],
            ],
            'email' => [
                'label' => 'Outgoing email',
                'fields' => [
                    'mail_from_address' => ['label' => 'From address', 'type' => 'email', 'help' => 'Mail server credentials stay in .env (MAIL_MAILER / HOST / USERNAME / PASSWORD).'],
                    'mail_from_name' => ['label' => 'From name', 'type' => 'text'],
                ],
            ],
            'payments' => [
                'label' => 'Payments',
                'fields' => [
                    'currency' => ['label' => 'Currency code', 'type' => 'text', 'help' => 'ISO 4217 lowercase (e.g. ngn). Must match Stripe.'],
                    'currency_locale' => ['label' => 'Currency locale', 'type' => 'text', 'help' => 'e.g. en_NG'],
                ],
            ],
            'reminders' => [
                'label' => 'Payment reminders',
                'fields' => [
                    'reminder_due_soon_days' => ['label' => 'Due-soon reminder (days before)', 'type' => 'number'],
                    'reminder_overdue_days_1' => ['label' => 'First overdue reminder (days after)', 'type' => 'number'],
                    'reminder_overdue_days_2' => ['label' => 'Second overdue reminder (days after)', 'type' => 'number'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public static function defaults(): array
    {
        $defaults = [];

        foreach (self::schema() as $group) {
            foreach ($group['fields'] as $key => $field) {
                $defaults[$key] = match ($key) {
                    'app_name' => 'Bamado Gym',
                    'tagline' => 'Train Hard. Live Strong.',
                    'hero_subtitle' => "Bamado Gym is Lagos's premier open-access fitness facility. State-of-the-art equipment, expert guidance, and a community that pushes you further.",
                    'about_heading' => 'Everything you need to reach your goals',
                    'about_blurb' => 'No excuses. No limits. Just results.',
                    'contact_address' => '123 Fitness Road, Lekki, Lagos',
                    'contact_phone' => '+234 800 000 0000',
                    'contact_email' => 'info@bamadogym.com',
                    'hours_weekday' => '5:00 AM – 10:00 PM',
                    'hours_saturday' => '6:00 AM – 8:00 PM',
                    'hours_sunday' => '8:00 AM – 4:00 PM',
                    'hours_holiday' => '8:00 AM – 2:00 PM',
                    'mail_from_address' => 'hello@bamadogym.com',
                    'mail_from_name' => 'Bamado Gym',
                    'currency' => 'ngn',
                    'currency_locale' => 'en_NG',
                    'reminder_due_soon_days' => '7',
                    'reminder_overdue_days_1' => '3',
                    'reminder_overdue_days_2' => '7',
                    default => null,
                };
            }
        }

        return $defaults;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        if (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '') {
            return $all[$key];
        }

        return $default ?? (self::defaults()[$key] ?? null);
    }

    /**
     * @return array<string, string|null>
     */
    public static function all(): array
    {
        try {
            if (! Schema::hasTable('settings')) {
                return self::defaults();
            }

            return Cache::remember(self::CACHE_KEY, 300, function () {
                return array_merge(
                    self::defaults(),
                    Setting::query()->pluck('value', 'key')->all()
                );
            });
        } catch (Throwable) {
            return self::defaults();
        }
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? null : (string) $value]
            );
        }

        self::forgetCache();
        self::applyToConfig();
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function applyToConfig(): void
    {
        $settings = self::all();

        if (! empty($settings['app_name'])) {
            config(['app.name' => $settings['app_name']]);
        }

        if (! empty($settings['mail_from_address'])) {
            config(['mail.from.address' => $settings['mail_from_address']]);
        }

        if (! empty($settings['mail_from_name'])) {
            config(['mail.from.name' => $settings['mail_from_name']]);
        }

        if (! empty($settings['currency'])) {
            config(['cashier.currency' => strtolower($settings['currency'])]);
        }

        if (! empty($settings['currency_locale'])) {
            config(['cashier.currency_locale' => $settings['currency_locale']]);
        }
    }
}
