<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

use Yard\DigiD\DigiD\Claim\ClaimInterface;

abstract class AbstractStatusCode implements StatusCodeInterface
{
    /** @var Status */
    protected $statusCode;

    public function __construct(ClaimInterface $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode->getStatus();
    }
}
