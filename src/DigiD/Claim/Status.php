<?php

namespace Yard\DigiD\Claim;

class Status implements ClaimInterface
{
    use StatusTrait;

    /**
     * Status Codes
     */
    const STATUS_SUCCESS              = 'urn:oasis:names:tc:SAML:2.0:status:Success';
    const STATUS_REQUESTER            = 'urn:oasis:names:tc:SAML:2.0:status:Requester';
    const STATUS_RESPONDER            = 'urn:oasis:names:tc:SAML:2.0:status:Responder';
    const STATUS_VERSION_MISMATCH     = 'urn:oasis:names:tc:SAML:2.0:status:VersionMismatch';
    const STATUS_NO_PASSIVE           = 'urn:oasis:names:tc:SAML:2.0:status:NoPassive';
    const STATUS_PARTIAL_LOGOUT       = 'urn:oasis:names:tc:SAML:2.0:status:PartialLogout';
    const STATUS_PROXY_COUNT_EXCEEDED = 'urn:oasis:names:tc:SAML:2.0:status:ProxyCountExceeded';
    const STATUS_REQUEST_DENIED       = 'urn:oasis:names:tc:SAML:2.0:status:RequestDenied';
    const STATUS_REQUEST_INCORRECT    = 'urn:oasis:names:tc:SAML:2.0:status:RequestIncorrect';

    /** @var string */
    protected $status = '';

    /** @var string */
    protected $fullStatus = '';

    /** @var array */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data   = $data;
        if (!empty($this->data)) {
            $this->fullStatus = $this->getMeaningfulStatusCode($this->data);
            $this->status     = $this->parseStatus($this->fullStatus);
        }
    }
}
