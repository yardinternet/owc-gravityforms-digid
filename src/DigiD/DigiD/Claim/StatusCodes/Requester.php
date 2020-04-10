<?php

namespace Yard\DigiD\DigiD\Claim\StatusCodes;

use function Yard\DigiD\Foundation\Helpers\config;

class Requester extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }

    public function message(): string
    {
        return __('Unknown error', config('core.text_domain'));
    }
}
