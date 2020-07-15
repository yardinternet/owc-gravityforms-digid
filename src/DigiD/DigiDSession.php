<?php

namespace Yard\DigiD;

class DigiDSession
{
    /** @var int */
    protected $sessionLifeTime;

    /** @var int */
    protected $sessionResumeLifeTime;

    public function __construct(int $sessionLifeTime = 0, int $sessionResumeLifeTime = 0)
    {
        $this->sessionLifeTime       = (int) $sessionLifeTime;
        $this->sessionResumeLifeTime = (int) $sessionResumeLifeTime;
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

    /**
     * Validate value and convert to minutes
     *
     * @return int
     */
    public function getSessionResumeLifeTime(): int
    {
        return 0 < $this->sessionResumeLifeTime ? $this->sessionResumeLifeTime * 60 : 5 * 60;
    }
}
