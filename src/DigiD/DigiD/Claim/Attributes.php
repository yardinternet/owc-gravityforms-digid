<?php

namespace Yard\DigiD\DigiD\Claim;

use SimpleXMLElement;
use Wizkunde\SAMLBase\Configuration\SessionID;

class Attributes
{
    const ARTIFACT_STATUSCODE = '//samlp:ArtifactResponse//samlp:Status//samlp:StatusCode';
    const LOGOUT_STATUSCODE   = '//samlp:LogoutResponse//samlp:Status//samlp:StatusCode';
    const NAME_ID             = '//samlp:ArtifactResponse//samlp:Response//saml:Assertion//saml:Subject//saml:NameID';

    /** @var string */
    protected $response;

    /** @var SimpleXMLElement */
    protected $xml;


    /** @var string $response */
    public function __construct(string $response)
    {
        $this->response = $response;
        $this->xml      = simplexml_load_string($this->response);
        $this->xml->registerXPathNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $this->xml->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
    }

    /**
     * Get the BSN object
     *
     * @return BSN
     */
    public function bsn(): BSN
    {
        return new BSN($this->query(self::NAME_ID));
    }

    /**
     * Return status code of response.
     *
     * @return Status
     */
    public function status(): Status
    {
        return new Status($this->query(self::ARTIFACT_STATUSCODE));
    }

    /**
     * Return logout status of response.
     *
     * @return Logout
     */
    public function logout(): Logout
    {
        return new Logout($this->query(self::LOGOUT_STATUSCODE));
    }

    /**
     * Return session ID.
     *
     * @return string
     */
    public function sessionID(): string
    {
        return (new SessionID())->getSessionIdFromDocument($this->response);
    }

    /**
     * Query the XML
     *
     * @param string $path
     * @return array
     */
    protected function query(string $path): array
    {
        return $this->xml->xpath($path);
    }
}
