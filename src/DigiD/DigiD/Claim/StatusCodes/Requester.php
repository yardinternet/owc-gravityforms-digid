<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

class Requester extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }
}
