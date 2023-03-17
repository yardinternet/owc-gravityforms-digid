<?php

namespace Tests\Yard\DigiD\Claim;

use Tests\Yard\DigiD\TestCase;
use TypeError;
use WP_Mock;
use Yard\DigiD\Claim\Attributes;
use Yard\DigiD\Claim\BSN;
use Yard\DigiD\Claim\Logout;
use Yard\DigiD\Claim\Status;

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
        $response = $this->loadStub('SUCCESS.xml');
        $attributes = new Attributes($response);
        $this->assertInstanceOf(Attributes::class, $attributes);
    }

    /** @test */
    public function query_returns_correctly()
    {
        $response = $this->loadStub('SUCCESS.xml');
        $attributes = new Attributes($response);
        $this->assertInstanceOf(Status::class, $attributes->status());
        $this->assertInstanceOf(BSN::class, $attributes->bsn());
        $this->assertInstanceOf(Logout::class, $attributes->logout());
    }

    /** @test */
    public function session_id_is_returned_correctly()
    {
        $response = $this->loadStub('SUCCESS.xml');
        $attributes = new Attributes($response);
        $this->assertEquals('26d224be1e436ba652b39b8cde970c5d4432583', $attributes->sessionID());
    }

    /** @test */
    public function if_xml_is_invalid()
    {
        $this->expectException(TypeError::class);
        new Attributes(false);
    }

    protected function loadStub($stub = '')
    {
        if (empty($stub)) {
            return '';
        }
        return file_get_contents(WP_PLUGIN_DIR .'/../Stubs/'. $stub);
    }
}
