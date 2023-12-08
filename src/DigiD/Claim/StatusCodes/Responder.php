<?php

namespace Yard\DigiD\Claim\StatusCodes;

use function Yard\DigiD\Foundation\Helpers\config;

class Responder extends AbstractStatusCode implements StatusCodeInterface
{
    public function isSuccess(): bool
    {
        return false;
    }

    public function message(): string
    {
        return __("Logging in to this organization failed. Please try again later. If you're still unable to log in, sign in to My DigiD. This will allow you to verify if your DigiD is working properly. There might be an issue with the organization where you are trying to log in.", config('core.text_domain'));
    }
}
