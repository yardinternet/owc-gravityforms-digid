<?php

namespace Yard\DigiD\DigiD\Claim;

class Logout extends AbstractClaim
{
    use StatusTrait;

    /** @var string */
    protected $status = '';

    /** @var string */
    protected $fullStatus = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        if (!empty($this->data)) {
            $this->fullStatus             = $this->getMeaningfullStatusCode($this->data);
            $this->status                 = $this->parseStatus($this->fullStatus);
        }
    }
}
