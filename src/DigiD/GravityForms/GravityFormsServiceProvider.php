<?php

namespace Yard\DigiD\GravityForms;

use Yard\DigiD\Foundation\ServiceProvider;

use function Yard\DigiD\Foundation\Helpers\resolve;

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

        add_filter('gform_pre_render', function ($form, $ajax, $field_values) {
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
        }, 10, 3);

        add_action('gform_after_submission', function ($entry, $form) {
            if (!count(array_filter($form['fields'], function ($item) {
                return  is_a($item, DigiDField::class);
            }))) {
                return;
            }

            resolve('session')->clear();
            resolve('teams')->info('Form is submitted; session is cleared');
        }, 10, 2);
    }
}
