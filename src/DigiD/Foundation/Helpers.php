<?php

declare(strict_types=1);

namespace Yard\DigiD\Foundation\Helpers;

use Yard\DigiD\Foundation\Plugin;

function app(): Plugin
{
    return resolve('app');
}

function storage_path($path): string
{
    return \ABSPATH . '../../storage/' . $path;
}

function resolve($container, $arguments = [])
{
    return \Yard\DigiD\Foundation\Plugin::getInstance()->getContainer()->get($container, $arguments);
}

function make($name, $container)
{
    return \Yard\DigiD\Foundation\Plugin::getInstance()->getContainer()->set($name, $container);
}

/**
 * @param string $setting
 * @param string $default
 *
 * @return string
 */
function config(string $setting, string $default = ''): ?string
{
    return resolve('config')->get($setting, $default);
}

function session_lifetime_in_seconds(): int
{
    return (int) config('digid.session.lifetime', GF_DIGID_DEFAULT_SESSION_LIFETIME_SECONDS);
}

/**
 * Dump the passed variables and end the script.
 *
 * @param  array|string|int  ...$args
 *
 * @return void
 */
function dd(...$args): void
{
    echo '<pre>';
    foreach ($args as $x) {
        var_dump($x);
    }
    echo '</pre>';
    die(1);
}

function view(string $template, array $vars = []): string
{
    return resolve(\Yard\DigiD\Foundation\View::class)->render($template, $vars);
}

function encrypt($string): string
{
    if (is_bool($string)) {
        return (string) $string;
    }

    if (empty($string)) {
        return '';
    }

    return resolve(\Yard\DigiD\Foundation\Cryptor::class)->encrypt($string);
}

function decrypt($string): ?string
{
    return resolve(\Yard\DigiD\Foundation\Cryptor::class)->decrypt($string) ?: null;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 *
 * @return mixed
 */
function value($value)
{
    return $value instanceof \Closure ? $value() : $value;
}

/**
 * Gets the value of an environment variable.
 *
 * @param  string  $key
 * @param  mixed   $default
 *
 * @return mixed
 */
function env($key, $default = null)
{
    $value = $_ENV[$key] ?? false;

    if (false === $value) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (1 < strlen($value) && startsWith($value, '"') && endsWith($value, '"')) {
        return substr($value, 1, -1);
    }

    return $value;
}

/**
 * Determine if a given string starts with a given substring.
 *
 * @param  string  $haystack
 * @param  string|array  $needles
 *
 * @return bool
 */
function startsWith($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if ('' !== $needle && substr($haystack, 0, strlen($needle)) === (string) $needle) {
            return true;
        }
    }

    return false;
}

/**
 * Determine if a given string ends with a given substring.
 *
 * @param  string  $haystack
 * @param  string|array  $needles
 *
 * @return bool
 */
function endsWith($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if (substr($haystack, -strlen($needle)) === (string) $needle) {
            return true;
        }
    }

    return false;
}

function rootCertificate(): ?String
{
    $root = config('digid.certificate.root');

    if ('' === $root || 'no-certificate' === $root) {
        return null;
    }

    return $root;
}
