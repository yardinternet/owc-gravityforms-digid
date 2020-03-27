<?php

namespace Yard\DigiD\DigiD\Claim;

use Exception;
use SimpleXMLElement;
use Yard\DigiD\DigiD\Claim\StatusCodes\StatusCodeInterface;

class Status
{
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

    /** @var array|string */
    protected $data;

    /** @var string */
    protected $status = '';

    /** @var string */
    protected $fullStatus = '';

    /**
     * @param array|SimpleXMLElement $data
     */
    public function __construct($data)
    {
        $this->data       = $data;
        $this->fullStatus = is_array($this->data) ? $this->getMeaningfullStatusCode($this->data) : $this->getFullStatus($this->data);
        $this->status     = $this->parseStatus($this->fullStatus);
    }

    /**
     * Get StatusCode class of response.
     *
     * @throws Exception
     * @return StatusCodeInterface
     */
    public function get(): StatusCodeInterface
    {
        $class = __NAMESPACE__ .'\\StatusCodes\\'. ucfirst($this->getStatus());
        if (!class_exists($class)) {
            throw new Exception($class .' does not exist');
        }

        return new $class($this);
    }

    /**
     * Get status of repsonse;
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the full status of the response.
     *
     * @param SimpleXMLElement $status
     *
     * @return string
     */
    private function getFullStatus(SimpleXMLElement $status): string
    {
        return json_decode(json_encode($status))->{'@attributes'}->{'Value'} ?? '';
    }

    /**
     * Parse the status code for the exact status.
     *
     * @param string $status
     *
     * @return string
     */
    private function parseStatus(string $status): string
    {
        $status = explode(':', $status);
        return end($status);
    }

    private function areEqual(array $statusCodes): bool
    {
        return 2 < count(array_unique($statusCodes));
    }

    protected function getMeaningfullStatusCode(array $statusCodes): string
    {
        $statusCodes = array_map(function ($item) {
            return $this->getFullStatus($item);
        }, $statusCodes);

        if ($this->areEqual($statusCodes)) {
            return $statusCodes[0];
        }

        return $statusCodes[1];
    }
}
