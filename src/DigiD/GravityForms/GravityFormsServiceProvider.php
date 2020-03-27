<?php

namespace Yard\DigiD\GravityForms;

use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\GravityForms\DigiD\DigiDField;

class GravityFormsServiceProvider extends ServiceProvider
{
    /**
     * Register all necessities for GravityForms.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('gform_loaded', function () {
            \GF_Fields::register(new DigiDField);
        }, 5);
    }
}
