<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

class Responder extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }
}
