<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gym marketing copy (MVP — no database)
    |--------------------------------------------------------------------------
    | Edit here or override with GYM_* env vars. No admin panel / settings table.
    */

    'app_name' => env('GYM_NAME', 'Bamado Gym'),
    'tagline' => env('GYM_TAGLINE', 'Train Hard. Live Strong.'),
    'hero_subtitle' => env(
        'GYM_HERO_SUBTITLE',
        "Bamado Gym is Lagos's premier open-access fitness facility. State-of-the-art equipment, expert guidance, and a community that pushes you further."
    ),
    'about_heading' => env('GYM_ABOUT_HEADING', 'Everything you need to reach your goals'),
    'about_blurb' => env('GYM_ABOUT_BLURB', 'No excuses. No limits. Just results.'),

    'contact_address' => env('GYM_ADDRESS', '123 Fitness Road, Lekki, Lagos'),
    'contact_phone' => env('GYM_PHONE', '+234 800 000 0000'),
    'contact_email' => env('GYM_EMAIL', 'info@bamadogym.com'),

    /*
    | Digits only, country code included (no +). Used for wa.me links.
    | Example: 2348012345678
    */
    'whatsapp' => env('GYM_WHATSAPP', '2348000000000'),

    'hours_weekday' => env('GYM_HOURS_WEEKDAY', '5:00 AM – 10:00 PM'),
    'hours_saturday' => env('GYM_HOURS_SATURDAY', '6:00 AM – 8:00 PM'),
    'hours_sunday' => env('GYM_HOURS_SUNDAY', '8:00 AM – 4:00 PM'),
    'hours_holiday' => env('GYM_HOURS_HOLIDAY', '8:00 AM – 2:00 PM'),

    'plans' => [
        [
            'name' => 'Monthly',
            'description' => 'Full gym access for one month.',
            'price_label' => '₦15,000',
            'duration' => '30 days',
            'featured' => false,
        ],
        [
            'name' => 'Quarterly',
            'description' => 'Full gym access for three months.',
            'price_label' => '₦40,000',
            'duration' => '90 days',
            'featured' => true,
        ],
        [
            'name' => 'Annual',
            'description' => 'Full gym access for a full year.',
            'price_label' => '₦140,000',
            'duration' => '365 days',
            'featured' => false,
        ],
    ],

];
