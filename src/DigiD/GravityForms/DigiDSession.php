<?php

namespace Yard\DigiD\GravityForms;

class DigiDSession
{
    public function __construct($sessionLifeTime = 0, $sessionResumeLifeTime = 0)
    {
        $this->sessionLifeTime = (int) $sessionLifeTime;
        $this->sessionResumeLifeTime = (int) $sessionResumeLifeTime;
    }

    /**
     * Validate value and convert to minutes
     *
     * @return int
     */
    public function getSessionLifeTime(): int
    {
        return $this->sessionLifeTime > 0 ? $this->sessionLifeTime * 60 : 5 * 60;
    }

    /**
     * Validate value and convert to minutes
     *
     * @return int
     */
    public function getSessionResumeLifeTime(): int
    {
        return $this->sessionResumeLifeTime > 0 ? $this->sessionResumeLifeTime * 60 : 5 * 60;
    }
}
