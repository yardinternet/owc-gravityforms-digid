<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

class Success extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return true;
    }

    public function message(): string
    {
        return '';
    }
}
