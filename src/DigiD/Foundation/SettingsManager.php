<?php

namespace Yard\DigiD\Foundation;

abstract class SettingsManager
{
    /** @var string */
    protected $key = '';

    /** @var string */
    protected $settings;

    public function __construct(string $key = '')
    {
        if (! empty($key)) {
            $this->key     = $key;
        }
    }

    /**
     * Static constructor for quick setup.
     *
     * @var string $key
     *
     * @return self
     */
    public static function make(string $key = ''): self
    {
        $class = get_called_class();
        return new $class($key);
    }

    /**
     * Save the data to the database.
    *
    * @param array $data
    *
    * @return boolean
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
     * @return array[]
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
     * @return string|array
     */
    public function find($key, $default = [])
    {
        return $this->get($key, $default);
    }
}
