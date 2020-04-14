<?php

namespace Yard\DigiD\GravityForms;

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
        $gravityForm = new GravityForms;
        $this->plugin->loader->addAction('gform_loaded', $gravityForm, 'registerField', 5);
        $this->plugin->loader->addFilter('gform_pre_render', $gravityForm, 'clearFormOnFirstRender', 10, 3);
        $this->plugin->loader->addAction('gform_after_submission', $gravityForm, 'clearFormAfterSubmission', 10, 2);
    }
}
