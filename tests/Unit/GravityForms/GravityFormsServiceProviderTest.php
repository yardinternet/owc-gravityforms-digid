<?php

namespace Yard\Tests\DigiD\GravityForms;

use Mockery as m;
use WP_Mock;
use Yard\DigiD\Foundation\Config;
use Yard\DigiD\Foundation\Loader;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\GravityForms\GravityForms;
use Yard\DigiD\GravityForms\GravityFormsServiceProvider;
use Yard\Tests\DigiD\TestCase;

class LoaderTest extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();

        $this->config         = m::mock(Config::class);
        $this->plugin         = m::mock(Plugin::class);
        $this->plugin->config = $this->config;
        $this->plugin->loader = m::mock(Loader::class);

        $this->service = new GravityFormsServiceProvider($this->plugin);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function if_service_provider_is_correctly_setup()
    {
        $this->plugin->loader->shouldReceive('addAction')->withArgs([
            'gform_loaded',
            GravityForms::class,
            'registerField',
            5,
        ])->once();

        $this->plugin->loader->shouldReceive('addFilter')->withArgs([
            'gform_pre_render',
            GravityForms::class,
            'clearFormOnFirstRender',
            10,
            3
        ])->once();

        $this->plugin->loader->shouldReceive('addAction')->withArgs([
            'gform_after_submission',
            GravityForms::class,
            'clearFormAfterSubmission',
            10,
            2
        ])->once();

        $this->service->register();

        $this->assertTrue(true);
    }
}
