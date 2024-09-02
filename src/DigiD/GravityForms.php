<?php

namespace Yard\DigiD;

use GFFormDisplay;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\encrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;

class GravityForms
{
    /**
     * Register Field
     */
    public function registerField(): void
    {
        \GF_Fields::register(new DigiDField);
    }

    /**
     * Clears session on first render.
     */
    public function clearFormOnFirstRender(array $form): array
    {
        if (!count(array_filter($form['fields'], function ($item) {
            return is_a($item, DigiDField::class);
        }))) {
            return $form;
        }

        if ($this->hasToken()) {
            return $form;
        }

        return $form;
    }

    public function clearFormAfterSubmission(array $entry, array $form): void
    {
        if (!count(array_filter($form['fields'], function ($item) {
            return is_a($item, DigiDField::class);
        }))) {
            return;
        }
    }

    /**
     * Remove the submit button and auto submit the form when the form only consists of a single DigiD field.
     */
    public function handleSubmitIfLoginOnlyForm(string $button, array $form): string
    {
        $specific_field_label = 'digid';
        $is_specific_field_present = false;

        // Count the number of visible fields in the form
        $visible_fields_count = 0;

        foreach ($form['fields'] as $field) {
            // Count only visible fields
            if (! $field->isHidden) {
                $visible_fields_count++;
            }

            // Check if the current field's label matches the specific field label
            if ($field->type == $specific_field_label && ! $field->isHidden) {
                $is_specific_field_present = true;
            }
        }

        // If the specific field is present, and it's the only visible field, hide the submit button
        if ($is_specific_field_present && 1 == $visible_fields_count) {
			$bsn = resolve('session')->getSegment('digid')->get('bsn', '');

			if (!empty($bsn)) {
				\GFAPI::submit_form($field->formId, array());
			}

			return '';
        }

        // Otherwise, return the original submit button
        return $button;
    }

	/**
     * Prepend custom div element for countdown
     */
    public function addCountDownHTML(string $form_tag, array $form): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn', '');

        if (!empty($bsn)) {
            $bsn = encrypt($bsn);
        }

        $logout = '';

        if (!empty($bsn)) {
            $logout = view('digid/logout.php', [
                'logoutLink' => config('digid.url.logout'),
            ]);
        }

        return $logout . $form_tag;
    }

    /**
     * When multiple IDPs (Identity Providers) are on the same form, the other one is optional.
     */
    public function optionalIDPs(array $result, $value, array $form, \GF_Field $field): array
    {
        // Check if there are other IDPs in the session.
        $eherkenning_in_session = resolve('session')->getSegment('eherkenning')->get('kvk');
        $eidas_in_session = resolve('session')->getSegment('eidas')->get('bsn');

        $field_type_digid = 'digid';
        $field_type_eherkenning = 'eherkenning';
        $field_type_eidas = 'eidas';

        // Check if the form contains the eHerkenning or eIDAS fields.
        $contains_field = false;
        foreach ($form['fields'] as $form_field) {
            if ($form_field->type == $field_type_eherkenning || $field_type_eidas) {
                $contains_field = true;
                break;
            }
        }

        // If eHerkenning or eIDAS are in the session and the form contains the field, DigiD is optional.
        if (($eherkenning_in_session || $eidas_in_session) && $contains_field) {
            if ($field->type == $field_type_digid) {
                $result['is_valid'] = true;
                $result['message'] = '';
            }
        }

        return $result;
    }

    protected function hasToken(): bool
    {
        return isset($_REQUEST['gf_token']) and (!empty($_REQUEST['gf_token']));
    }

    protected function isFormPaginated(array $form): bool
    {
        if (!isset($form['pagination']['pages'])) {
            return false;
        }
        if (is_null($form['pagination']['pages'])) {
            return false;
        }
        return (1 >= count($form['pagination']['pages']));
    }

    protected function isFirstPaginatedView(array $form): bool
    {
        return (($this->getCurrentPage($form) === $this->getSourcePage($form))
            and $this->isFirstPage($form));
    }

    protected function getSourcePage(array $form): int
    {
        return (int) GFFormDisplay::get_source_page($form['id']);
    }

    protected function getCurrentPage(array $form): int
    {
        return (int) GFFormDisplay::get_current_page($form['id']);
    }

    protected function isFirstPage(array $form): bool
    {
        return (1 === GFFormDisplay::get_current_page($form['id']));
    }

    protected function isLastPage(array $form): bool
    {
        $pageNumber = GFFormDisplay::get_current_page($form['id']);
        $lastPage = count($form['pagination']['pages']);

        return $pageNumber >= $lastPage;
    }
}
