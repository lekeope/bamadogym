<?php

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return config('gym.'.$key, $default);
    }
}

if (! function_exists('gym_whatsapp_url')) {
    function gym_whatsapp_url(?string $message = null): string
    {
        $digits = preg_replace('/\D+/', '', (string) config('gym.whatsapp', '')) ?: '2348000000000';
        $url = 'https://wa.me/'.$digits;

        if ($message) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}

if (! function_exists('gym_mailto_url')) {
    function gym_mailto_url(?string $subject = null): string
    {
        $email = (string) config('gym.contact_email', 'info@bamadogym.com');
        $url = 'mailto:'.$email;

        if ($subject) {
            $url .= '?subject='.rawurlencode($subject);
        }

        return $url;
    }
}
