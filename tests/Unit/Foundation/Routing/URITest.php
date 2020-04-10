<?php

namespace Yard\Tests\DigiD\Foundation;

use WP_Mock;
use Yard\DigiD\Foundation\Routing\URI;
use Yard\Tests\DigiD\TestCase;

class URITest extends TestCase
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
    public function uri_removes_query()
    {
        $uri = new URI('test');
        $this->assertEquals('test', $uri->removeQuery());

        $uri = new URI('test?q=q');
        $this->assertEquals('test', $uri->removeQuery());

        $this->assertEquals('test', $uri);
    }
}
