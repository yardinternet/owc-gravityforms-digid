<?php

namespace Yard\Tests\DigiD;

use Mockery as m;
use WP_Mock;
use Yard\DigiD\DigiDServiceProvider;
use Yard\DigiD\Foundation\Loader;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\GravityForms;

class DigiDServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();

        $this->plugin         = m::mock(Plugin::class);
        $this->plugin->shouldReceive('getLoader')->twice()->andReturn(m::mock(Loader::class));

        $this->service = new DigiDServiceProvider($this->plugin);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function if_service_provider_is_correctly_setup()
    {
        $this->plugin->getLoader()->shouldReceive('addAction')->withArgs([
            'gform_loaded',
            GravityForms::class,
            'registerField',
            5,
        ])->once();

        WP_Mock::userFunction('is_admin', [
            'times'  => 1,
            'return' => true
        ]);

        $this->service->register();

        $this->assertTrue(true);
    }
}
