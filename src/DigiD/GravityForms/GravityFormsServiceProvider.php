<?php

namespace Yard\DigiD\GravityForms;

use GFAddOn;
use GFForms;
use Yard\DigiD\Foundation\ServiceProvider;

class GravityFormsServiceProvider extends ServiceProvider
{
    /**
     * Register all necessities for GravityForms.
     *
     * @return void
     */
    public function register(): void
    {
        if (! method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        GFForms::include_addon_framework();
        GFAddOn::register(\Yard\DigiD\GravityForms\GravityFormsAddon::class);
        GravityFormsAddon::get_instance();


        $gravityForm = new GravityForms;
        $this->plugin->loader->addAction('gform_loaded', $gravityForm, 'registerField', 5);
        $this->plugin->loader->addFilter('gform_pre_render', $gravityForm, 'clearFormOnFirstRender', 10, 3);
        $this->plugin->loader->addAction('gform_after_submission', $gravityForm, 'clearFormAfterSubmission', 10, 2);

        $this->registerSettingsAddon();
    }


    private function registerSettingsAddon(): void
    {
        if (! method_exists('\GFForms', 'include_addon_framework')) {
            return;
        }

        \GFForms::include_addon_framework();
        \GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }
}
