<?php

namespace Yard\DigiD\DigiD\Claim;

class Attributes
{
    /** @var string */
    protected $response;

    /** @var string $response */
    public function __construct(string $response)
    {
        $this->response = simplexml_load_string($response);
        $this->response->registerXPathNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $this->response->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
    }

    /**
     * Get the BSN object
     *
     * @return BSN
     */
    public function bsn(): BSN
    {
        try {
            return new BSN($this->response->xpath('//samlp:ArtifactResponse//samlp:Response//saml:Assertion//saml:Subject//saml:NameID'));
        } catch (\Exception $e) {
            return 'Fout ontstaan';
        }
    }

    /**
     * Return status code of response.
     *
     * @return string|null
     */
    public function status(): Status
    {
        return new Status($this->response->xpath('//samlp:ArtifactResponse//samlp:Status//samlp:StatusCode'));
    }
}
