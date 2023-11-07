<?php

namespace Yard\DigiD;

class DigiDSession
{
    /** @var int */
    protected $sessionLifeTime;

    public function __construct(int $sessionLifeTime = 0)
    {
        $this->sessionLifeTime = (int) $sessionLifeTime;
    }

    /**
     * Validate value and convert to minutes
     *
     * @return int
     */
    public function getSessionLifeTime(): int
    {
        return 0 < $this->sessionLifeTime ? $this->sessionLifeTime * 60 : 5 * 60;
    }
}
