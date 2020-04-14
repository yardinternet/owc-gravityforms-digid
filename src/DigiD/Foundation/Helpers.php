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
    return resolve(\Yard\DigiD\Foundation\Cryptor::class)->encrypt($string);
}

function decrypt($string): string
{
    return resolve(\Yard\DigiD\Foundation\Cryptor::class)->decrypt($string);
}
