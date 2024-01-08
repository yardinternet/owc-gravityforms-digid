<?php

namespace Yard\DigiD\Foundation;

abstract class SettingsManager
{
    protected string $key = '';
    protected string $settings;

    public function __construct(string $key = '')
    {
        if (! empty($key)) {
            $this->key = $key;
        }
    }

    /**
     * Static constructor for quick setup.
     */
    public static function make(string $key = ''): self
    {
        $class = get_called_class();

        return new $class($key);
    }

    /**
     * Save the data to the database.
     */
    public function save(array $data): bool
    {
        return \update_option($this->key, $data);
    }

    /**
     * Get the attributes.
     *
     * @param array $default
     *
     * @return mixed
     */
    public function all($default = [])
    {
        return \get_option($this->key, $default);
    }

    /**
     * @param string $key
     * @param array $default
     *
     * @return mixed
     */
    public function get(string $key, $default = [])
    {
        $all = $this->all($default);

        return $all[$key] ?? $all;
    }

    /**
     * Find a specific value by key.
     *
     * @param string $key
     * @param array $default
     *
     * @return mixed
     */
    public function find($key, $default = [])
    {
        return $this->get($key, $default);
    }
}
