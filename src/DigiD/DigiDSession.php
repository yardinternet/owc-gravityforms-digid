<?php

declare(strict_types=1);

namespace Yard\DigiD;

class DigiDSession
{
    public function __construct(protected int $sessionLifeTime = 0)
    {
    }

    /**
     * Validate value and convert to minutes
     */
    public function getSessionLifeTime(): int
    {
        return 0 < $this->sessionLifeTime ? $this->sessionLifeTime * 60 : 5 * 60;
    }
}
