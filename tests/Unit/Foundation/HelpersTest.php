<?php

namespace Tests\Yard\DigiD\Foundation;

use Tests\Yard\DigiD\TestCase;

use WP_Mock;

use function Yard\DigiD\Foundation\Helpers\storage_path;

class HelpersTest extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function storage_returns_correct_path(): void
    {
        define(ABSPATH, '/../../');
        $actual = storage_path('test');
        $this->assertEquals('../../storage/test', $actual);
    }
}
