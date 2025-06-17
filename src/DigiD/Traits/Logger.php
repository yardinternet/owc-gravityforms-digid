<?php

declare(strict_types=1);

namespace Yard\DigiD\Traits;

use Exception;
use Monolog\Level;
use function Yard\DigiD\Foundation\Helpers\resolve;

trait Logger
{
    public function logException(Exception $exception, array $context = []): void
    {
        try {
            $level = Level::from($exception->getCode());
            $method = $level->toPsrLogLevel();
        } catch (Exception $e) {
            $method = 'error';
        }

        /** @var Logger */
        $logger = resolve('logger');

        if (! method_exists($logger, $method)) {
            $method = 'error';
        }

        /**
         * Intercept the exception for further processing, such as logging to e.g. Sentry from the project itself.
         *
         * @param Exception $exception The exception to intercept.
         * @param string $method PSR‑3 log level name (e.g. 'error', 'debug').
         *
         * @since 2.0.0
         */
        do_action('owc_gravityforms_digid_exception_intercept', $exception, $method);

        if (! defined('WP_DEBUG') || ! WP_DEBUG) {
            return;
        }

        $logger->$method($exception->getMessage(), $context);
    }

    public function logInfo(string $message, array $context = []): void
    {
        if (! defined('WP_DEBUG') || ! WP_DEBUG) {
            return;
        }

        /** @var Logger */
        $logger = resolve('logger');
        $logger->info($message, $context);
    }
}
