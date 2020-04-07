<?php

namespace Yard\DigiD\DigiD\Claim;

abstract class AbstractClaim
{
    /** @var array */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data   = $data;
    }
}
