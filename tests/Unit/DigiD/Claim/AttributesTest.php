<?php

namespace Yard\Tests\DigiD\DigiD\Claim;

use WP_Mock;
use Yard\DigiD\DigiD\Claim\Attributes;
use Yard\DigiD\DigiD\Claim\BSN;
use Yard\DigiD\DigiD\Claim\Status;
use Yard\Tests\DigiD\TestCase;

class AttributesTest extends TestCase
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
    public function attributes_class_is_a_correct_instance()
    {
        $response   = $this->loadStub('SUCCESS.xml');
        $attributes = new Attributes($response);
        $this->assertInstanceOf(Attributes::class, $attributes);
    }

    /** @test */
    public function query_returns_correctly()
    {
        $response   = $this->loadStub('SUCCESS.xml');
        $attributes = new Attributes($response);
        $this->assertInstanceOf(Status::class, $attributes->status());
        $this->assertInstanceOf(BSN::class, $attributes->bsn());
    }


    protected function loadStub($stub = '')
    {
        if (empty($stub)) {
            return '';
        }
        return file_get_contents(WP_PLUGIN_DIR .'/../Stubs/'. $stub);
    }
}
