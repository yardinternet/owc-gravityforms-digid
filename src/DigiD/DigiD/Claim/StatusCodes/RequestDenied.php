<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

class RequestDenied extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }
}
