<?php

declare(strict_types=1);

namespace Yard\DigiD\Claim;

use Exception;
use SimpleXMLElement;
use Yard\DigiD\Claim\StatusCodes\StatusCodeInterface;

trait StatusTrait
{
    /**
     * Get StatusCode class of response.
     *
     * @throws Exception
     *
     * @return StatusCodeInterface
     */
    public function get(): StatusCodeInterface
    {
        $class = __NAMESPACE__ .'\\StatusCodes\\'. ucfirst($this->getStatus());
        if (! class_exists($class)) {
            throw new Exception($class .' does not exist', 500);
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
        if (empty($this->status)) {
            throw new Exception('Empty class name is not allowed', 400);
        }

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

    /**
     * Returns the last and most meaningful statuscode.
     *
     * @param SimpleXMLElement[] $statusCodes
     *
     * @return string
     */
    protected function getMeaningfulStatusCode(array $statusCodes): string
    {
        $statusCodes = array_map(function ($item) {
            if (! is_a($item, SimpleXMLElement::class)) {
                return;
            }

            return $this->getFullStatus($item);
        }, $statusCodes);

        $statusCodes = \array_filter($statusCodes);
        $statusCodes = \array_unique($statusCodes);

        if (1 > count($statusCodes)) {
            return Status::STATUS_REQUEST_INCORRECT;
        }

        return end($statusCodes);
    }
}
