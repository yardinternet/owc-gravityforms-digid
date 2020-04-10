<?php

namespace Yard\DigiD\GravityForms;

use function Yard\DigiD\Foundation\Helpers\resolve;

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
        $this->plugin->loader->addAction('gform_loaded', $this, 'loadField', 5);
        $this->plugin->loader->addFilter('gform_pre_render', $this, 'clearFormOnFirstRender', 10, 3);
        $this->plugin->loader->addAction('gform_after_submission', $this, 'clearFormAfterSubmission', 10, 2);
    }

    public function loadField(): void
    {
        \GF_Fields::register(new DigiDField);
    }

    public function clearFormOnFirstRender($form, $ajax, $field_values)
    {
        if (!count(array_filter($form['fields'], function ($item) {
            return  is_a($item, DigiDField::class);
        }))) {
            return $form;
        }

        if (isset($_REQUEST['gf_token']) and (!empty($_REQUEST['gf_token']))) {
            return $form;
        }

        /** @TODO clear form at first load, not at every view */
        // resolve('session')->clear();
        // resolve('teams')->info('Form is render; session is cleared');

        return $form;
    }

    public function clearFormAfterSubmission($entry, $form)
    {
        if (!count(array_filter($form['fields'], function ($item) {
            return  is_a($item, DigiDField::class);
        }))) {
            return;
        }

        resolve('session')->clear();
        resolve('teams')->info('Form is submitted; session is cleared');
    }
}
