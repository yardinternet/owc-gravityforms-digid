<?php

namespace Yard\DigiD;

class GravityFormsSettings
{
    protected $prefix = 'owc-digid-';

    protected $name = 'gravityformsaddon_owc-gravityforms-digid_settings';

    protected $options;

    final private function __construct()
    {
        $this->options   = get_option($this->name, []);
    }

    /**
     * Static constructor
     *
     * @return self
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * Get the value from the database.
     *
     * @param string $key
     * @return string
     */
    public function get(string $key): string
    {
        $key   = $this->prefix . $key;
        return $this->options[$key] ?? '';
    }
}
