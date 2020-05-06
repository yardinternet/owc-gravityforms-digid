<?php

namespace Yard\DigiD\Claim\StatusCodes;

use function Yard\DigiD\Foundation\Helpers\config;

class AuthnFailed extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }

    public function message(): string
    {
        return __(
            'Login cancelled',
            config('core.text_domain')
        );
    }
}
