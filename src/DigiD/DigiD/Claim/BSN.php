<?php

namespace Yard\DigiD\DigiD\Claim;

use SimpleXMLElement;

class BSN
{
    /** @var array|string */
    protected $data;

    /** @var string */
    protected $nameID = '';

    /**
     * @param array|SimpleXMLElement $data
     */
    public function __construct($data)
    {
        $this->nameID = is_array($data) ? $this->getFullNameID($data[0]) : $this->getFullNameID($data);
        $this->data   = $data;
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
        return json_decode(json_encode($nameID))->{'0'} ?? '';
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
}
