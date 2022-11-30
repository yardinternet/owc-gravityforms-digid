<?php

namespace Tests\Yard\DigiD\Claim;

use Tests\Yard\DigiD\TestCase;
use WP_Mock;
use Yard\DigiD\Claim\BSN;

class BSNTest extends TestCase
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
        $bsn = new BSN();
        $this->assertInstanceOf(BSN::class, $bsn);
    }

    /** @test */
    public function it_returns_nothing_if_empty_data_is_provided()
    {
        $bsn = new BSN([]);
        $this->assertEmpty($bsn->getID());
    }

    /** @test */
    public function it_returns_a_valid_ID()
    {
        $response = $this->loadStub('SUCCESS.xml');
        $bsn = new BSN($response->xpath('//samlp:ArtifactResponse//samlp:Response//saml:Assertion//saml:Subject//saml:NameID'));
        $this->assertEquals('900192756', $bsn->getID());
    }

    /** @test */
    public function it_returns_a_valid_name_ID()
    {
        $response = $this->loadStub('SUCCESS.xml');
        $bsn = new BSN($response->xpath('//samlp:ArtifactResponse//samlp:Response//saml:Assertion//saml:Subject//saml:NameID'));
        $this->assertEquals('s00000000:900192756', $bsn->getNameID());
    }

    /** @test */
    public function it_returns_a_zero_if_denied()
    {
        $response = $this->loadStub('DENIED.xml');
        $bsn = new BSN($response->xpath('//samlp:ArtifactResponse//samlp:Response//saml:Assertion//saml:Subject//saml:NameID'));
        $this->assertEquals(0, $bsn->getID());
    }

    protected function loadStub($stub = '')
    {
        if (empty($stub)) {
            return '';
        }
        $responseData = file_get_contents(WP_PLUGIN_DIR .'/../Stubs/'. $stub);
        $response = simplexml_load_string($responseData);
        $response->registerXPathNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $response->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

        return $response;
    }
}
