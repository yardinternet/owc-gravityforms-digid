<?php

namespace Yard\DigiD\Claim\StatusCodes;

use function Yard\DigiD\Foundation\Helpers\config;

class Requester extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }

    public function message(): string
    {
        return sprintf(
            __(
                'An error has occurred in the communication with DigiD. Please try again later. If this error persists, please check the website %s for the latest information.',
                config('core.text_domain')
            ),
            '<a href="https://www.digid.nl" target="_blank">https://www.digid.nl</a>'
        );
    }
}
