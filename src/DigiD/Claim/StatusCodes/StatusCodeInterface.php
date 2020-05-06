<?php

namespace Yard\DigiD\Claim\StatusCodes;

interface StatusCodeInterface
{
    public function isSuccess(): bool;

    public function message(): string;
}
