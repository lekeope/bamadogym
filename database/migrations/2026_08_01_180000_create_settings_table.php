<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();

        $defaults = [
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
        ];

        DB::table('settings')->insert(
            collect($defaults)->map(fn ($value, $key) => [
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
