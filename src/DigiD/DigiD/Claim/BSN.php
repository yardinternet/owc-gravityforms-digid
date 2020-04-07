<?php

namespace Yard\DigiD\DigiD\Claim;

use SimpleXMLElement;

class BSN extends AbstractClaim
{
    /** @var string */
    protected $nameID = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        if (!empty($this->data)) {
            $this->nameID = is_array($this->data) ? $this->getFullNameID($this->data[0]) : $this->getFullNameID($this->data);
        }
    }

    /**
     * Get the full nameID of the response.
     *
     * @param SimpleXMLElement $nameID
     *
     * @return string
     */
    private function getFullNameID(SimpleXMLElement $nameID): string
    {
        return (string) $nameID[0] ?? '';
    }

    /**
     * Get the BSN number.
     *
     * @return integer
     */
    public function getID(): int
    {
        $bsn = explode(':', $this->nameID);
        return (int) end($bsn);
    }

    /**
     * Get the value of nameID
     *
     * @return string
     */
    public function getNameID(): string
    {
        return $this->nameID;
    }
}
