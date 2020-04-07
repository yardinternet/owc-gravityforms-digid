<?php

namespace Yard\DigiD\DigiD\Claim;

use Exception;
use SimpleXMLElement;
use Yard\DigiD\DigiD\Claim\StatusCodes\StatusCodeInterface;

trait StatusTrait
{
    /**
    * Get StatusCode class of response.
    *
    * @throws Exception
    * @return StatusCodeInterface
    */
    public function get(): StatusCodeInterface
    {
        $class = __NAMESPACE__ .'\\StatusCodes\\'. ucfirst($this->getStatus());
        if (!class_exists($class)) {
            throw new Exception($class .' does not exist');
        }

        return new $class($this);
    }

    /**
     * Get status of repsonse;
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the full status of the response.
     *
     * @param SimpleXMLElement $status
     *
     * @return string
     */
    private function getFullStatus(SimpleXMLElement $status): string
    {
        return (string) $status->attributes()->Value ?? '';
    }

    /**
     * Parse the status code for the exact status.
     *
     * @param string $status
     *
     * @return string
     */
    private function parseStatus(string $status): string
    {
        $status = explode(':', $status);
        return end($status);
    }

    private function areEqual(array $statusCodes): bool
    {
        return 2 > count(array_unique($statusCodes));
    }

    protected function getMeaningfullStatusCode(array $statusCodes): string
    {
        $statusCodes = array_map(function ($item) {
            return $this->getFullStatus($item);
        }, $statusCodes);

        if ($this->areEqual($statusCodes)) {
            return $statusCodes[0];
        }

        return $statusCodes[1];
    }
}
