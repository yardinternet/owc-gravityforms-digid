<?php

namespace Yard\Tests\DigiD\DigiD\Claim;

use Exception;
use WP_Mock;
use Yard\DigiD\DigiD\Claim\Logout;
use Yard\Tests\DigiD\TestCase;

class LogoutTest extends TestCase
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
    public function bsn_class_is_a_correct_instance()
    {
        $logout = new Logout();
        $this->assertInstanceOf(Logout::class, $logout);
    }

    /** @test */
    public function it_returns_nothing_if_empty_data_is_provided()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty class name is not allowed');
        $logout   = new Logout([]);
        $this->assertEmpty($logout->getStatus());
    }

    /** @test */
    public function it_returns_an_error_if_invalid_data_is_provided()
    {
        $logout   = new Logout(['asdf']);
        $this->assertEquals('RequestIncorrect', $logout->getStatus());
    }

    protected function loadStub($stub = '')
    {
        if (empty($stub)) {
            return '';
        }
        $responseData         = file_get_contents(WP_PLUGIN_DIR .'/../Stubs/'. $stub);
        $response             = simplexml_load_string($responseData);
        $response->registerXPathNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $response->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

        return $response;
    }
}
