<?php

namespace Yard\DigiD\Foundation;

abstract class ServiceProvider
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Return Foundation plugin.
     */
    public function plugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * Register the service provider.
     */
    abstract public function register(): void;
}
