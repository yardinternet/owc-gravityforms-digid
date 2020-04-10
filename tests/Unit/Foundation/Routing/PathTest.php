<?php

namespace Yard\Tests\DigiD\Foundation;

use WP_Mock;
use Yard\DigiD\Foundation\Routing\Path;
use Yard\Tests\DigiD\TestCase;

class PathTest extends TestCase
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
    public function path_is_correctly_normalized()
    {
        $uri = new Path('test');
        $this->assertEquals('/test', $uri->normalize());

        $uri = new Path(' /test ');
        $this->assertEquals('/test', $uri->normalize());
    }

    /** @test */
    public function trailing_slash_is_correctly_removed()
    {
        $uri = new Path('test');
        $this->assertEquals('test', $uri->removeTrailingSlashes());

        $uri = new Path('test/');
        $this->assertEquals('test', $uri->removeTrailingSlashes());

        $uri = new Path('test/////');
        $this->assertEquals('test', $uri->removeTrailingSlashes());
    }

    /** @test */
    public function path_is_absolute()
    {
        $uri = new Path('/test');
        $this->assertTrue($uri->isAbsolute());

        $uri = new Path('test/');
        $this->assertFalse($uri->isAbsolute());

        $uri = new Path('');
        $this->assertFalse($uri->isAbsolute());

        $uri = new Path('test/////');
        $this->assertFalse($uri->isAbsolute());
    }

    /** @test */
    public function path_is_relative()
    {
        $uri = new Path('/test');
        $this->assertFalse($uri->isRelative());

        $uri = new Path('test/');
        $this->assertTrue($uri->isRelative());

        $uri = new Path('');
        $this->assertTrue($uri->isRelative());

        $uri = new Path('test/////');
        $this->assertTrue($uri->isRelative());
    }
}
