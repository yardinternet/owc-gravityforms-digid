<?php

namespace Yard\DigiD\Foundation;

abstract class ServiceProvider
{

    /**
     * Instance of the plugin.
     *
     * @var \Yard\DigiD\Foundation\Plugin
     */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Return Foundation plugin.
     *
     * @return \Yard\DigiD\Foundation\Plugin
     */
    public function plugin(): \Yard\DigiD\Foundation\Plugin
    {
        return $this->plugin;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register(): void;
}
