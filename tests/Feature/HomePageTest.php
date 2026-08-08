<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_renders(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Bamado Gym', false);
        $response->assertSee('WhatsApp', false);
        $response->assertSee('Simple, transparent pricing', false);
    }
}
