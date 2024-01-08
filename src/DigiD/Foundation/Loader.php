<?php

namespace Yard\DigiD\Foundation;

class Loader
{

    /**
     * The array of actions registered with WordPress.
     */
    protected array $actions = [];

    /**
     * The array of filters registered with WordPress.
     */
    protected array $filters = [];

    /**
     * Retrieves an instance of the loader, and creates one if it doesn't exist.
     */
    public static function getInstance(): self
    {
        static $inst = null;
        if (null === $inst) {
            $inst = new Loader();
        }

        return $inst;
    }

    /**
     * Return the collection of actions.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Return the collection of filters.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     */
    public function addAction(string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     */
    public function addFilter(string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     */
    protected function add($hooks, $hook, $component, $callback, $priority, $accepted_args): array
    {
        $hooks[] = [
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        ];

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     */
    public function register(): void
    {
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                [ $hook['component'], $hook['callback'] ],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                [ $hook['component'], $hook['callback'] ],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}
