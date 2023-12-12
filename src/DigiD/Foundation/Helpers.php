<?php

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

/**
 * Dump the passed variables and end the script.
 *
 * @param  array|string|int  ...$args
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

function decrypt($string): string
{
    return resolve(\Yard\DigiD\Foundation\Cryptor::class)->decrypt($string);
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
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

/**
 * Simulate a `glob()` with the `GLOB_BRACE` flag set. For systems that lack it, e.g. Alpine Linux / Docker.
 * Copied and adapted from Zend Framework's `Glob::fallbackGlob()` and Glob::nextBraceSub()`.
 *
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 *
 * @param string $pattern Filename pattern.
 * @param void $dummy_flags Not used.
 *
 * @return array Array of paths.
 */
function globBrace($pattern, $dummyFlags = null) {
    static $nextBraceSub;

    if (!$nextBraceSub) {
        // Find the end of the subpattern in a brace expression.
        $nextBraceSub = function ($pattern, $current) {
            $length = strlen($pattern);
            $depth = 0;

            while ($current < $length) {
                if ('\\' === $pattern[$current]) {
                    if (++$current === $length) {
                        break;
                    }
                    $current++;
                } else {
                    if (('}' === $pattern[$current] && $depth-- === 0) || (',' === $pattern[$current] && 0 === $depth)) {
                        break;
                    } elseif ('{' === $pattern[$current++]) {
                        $depth++;
                    }
                }
            }

            return $current < $length ? $current : null;
        };
    }

    $length = strlen($pattern);

    // Find first opening brace.
    for ($begin = 0; $begin < $length; $begin++) {
        if ('\\' === $pattern[$begin]) {
            $begin++;
        } elseif ('{' === $pattern[$begin]) {
            break;
        }
    }

    // Find comma or matching closing brace.
    if (null === ($next = $nextBraceSub($pattern, $begin + 1))) {
        return glob($pattern);
    }

    $rest = $next;

    // Point `$rest` to matching closing brace.
    while ('}' !== $pattern[$rest]) {
        if (null === ($rest = $nextBraceSub($pattern, $rest + 1))) {
            return glob($pattern);
        }
    }

    $paths = array();
    $p = $begin + 1;

    // For each comma-separated subpattern.
    do {
        $subpattern = substr($pattern, 0, $begin)
            . substr($pattern, $p, $next - $p)
            . substr($pattern, $rest + 1);

        if (($result = globBrace($subpattern))) {
            $paths = array_merge($paths, $result);
        }

        if ('}' === $pattern[$next]) {
            break;
        }

        $p = $next + 1;
        $next = $nextBraceSub($pattern, $p);
    } while (null !== $next);

    return array_values(array_unique($paths));
}
