<?php

use App\Services\AppSettings;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return AppSettings::get($key, $default);
    }
}
