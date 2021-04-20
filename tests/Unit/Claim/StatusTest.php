<?php

namespace Tests\Yard\DigiD\Claim;

use Exception;
use WP_Mock;
use Yard\DigiD\Claim\Status;
use Yard\DigiD\Claim\StatusCodes\RequestDenied;
use Yard\DigiD\Claim\StatusCodes\Success;
use Tests\Yard\DigiD\TestCase;

class StatusTest extends TestCase
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
    public function status_class_is_a_correct_instance()
    {
        $status = new Status();
        $this->assertInstanceOf(Status::class, $status);
    }

    /** @test */
    public function it_returns_nothing_if_empty_data_is_provided()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty class name is not allowed');
        $status   = new Status([]);
        $this->assertEmpty($status->getStatus());
    }

    /** @test */
    public function it_returns_an_error_if_invalid_data_is_provided()
    {
        $status   = new Status(['asdf']);
        $this->assertEquals('RequestIncorrect', $status->getStatus());
    }

    /** @test */
    public function it_returns_a_valid_status()
    {
        $response        = $this->loadStub('SUCCESS.xml');
        $status          = new Status($response->xpath('//samlp:ArtifactResponse//samlp:Status//samlp:StatusCode'));
        $this->assertEquals('Success', $status->getStatus());
        $this->assertInstanceOf(Success::class, $status->get());
        $this->assertTrue($status->get()->isSuccess());
        $this->assertEquals('Success', $status->get()->getStatusCode());
    }

    /** @test */
    public function it_returns_a_valid_status_if_denied()
    {
        $response        = $this->loadStub('DENIED.xml');
        $status          = new Status($response->xpath('//samlp:ArtifactResponse//samlp:Status//samlp:StatusCode'));
        $this->assertEquals('RequestDenied', $status->getStatus());
        $this->assertInstanceOf(RequestDenied::class, $status->get());
        $this->assertFalse($status->get()->isSuccess());
        $this->assertEquals('RequestDenied', $status->get()->getStatusCode());
    }

    /** @test */
    public function it_throws_exception_when_wrong_status_if_returned()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Yard\DigiD\Claim\StatusCodes\WrongStatusCode does not exist');
        $response        = $this->loadStub('WRONG-STATUS.xml');
        $status          = new Status($response->xpath('//samlp:ArtifactResponse//samlp:Status//samlp:StatusCode'));
        $status->get();
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
