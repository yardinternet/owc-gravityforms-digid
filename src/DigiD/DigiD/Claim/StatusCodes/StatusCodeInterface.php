<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

interface StatusCodeInterface
{
    public function isSuccess(): bool;
}
