<?php

namespace Yard\DigiD\DigiD\Claim;

class Logout implements ClaimInterface
{
    use StatusTrait;

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
            $this->fullStatus             = $this->getMeaningfulStatusCode($this->data);
            $this->status                 = $this->parseStatus($this->fullStatus);
        }
    }
}
