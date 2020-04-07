<?php

namespace Yard\DigiD\DigiD\Claim;

class Status extends AbstractClaim
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

    /** @var string */
    protected $status = '';

    /** @var string */
    protected $fullStatus = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        if (!empty($this->data)) {
            $this->fullStatus = $this->getMeaningfullStatusCode($this->data);
            $this->status     = $this->parseStatus($this->fullStatus);
        }
    }
}
