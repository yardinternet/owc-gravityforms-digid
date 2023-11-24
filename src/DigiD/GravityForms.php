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
     *
     * @return void
     */
    public function registerField(): void
    {
        \GF_Fields::register(new DigiDField);
    }

    /**
     * Clears session on first render.
     *
     * @param array $form
     *
     * @return array
     */
    public function clearFormOnFirstRender(array $form): array
    {
        if (!count(array_filter($form['fields'], function ($item) {
            return  is_a($item, DigiDField::class);
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
     * Prepend custom div element for countdown
     *
     * @param string $form_tag
     * @param array $form
     *
     * @return string
     */
    public function addCountDownHTML($form_tag, $form): string
    {
		$bsn = resolve('session')->getSegment('digid')->get('bsn', '');

        if (!empty($bsn)) {
            $bsn = encrypt($bsn);
        }

        $logout = '';

        if (!empty($bsn)) {
            $digiDSession = new DigiDSession(config('digid.session.lifetime'));
            $logout = view('digid/logout.php', [
                'logoutLink'            => config('digid.url.logout'),
                'SessionLifeTime'       => $digiDSession->getSessionLifeTime(),
				'LastActivity'          => resolve('session')->getSegment('digid')->get('lastActivity'),
            ]);
        }

        return $logout . $form_tag;
    }

	/**
	 * When two IDPs (Identity Providers) are on the same form, the other one is optional.
	 *
	 * @return void
	 */
	public function optionalIDPs($result, $value, $form, $field)
	{
		// Check if eHerkenning is in the session.
		$eherkenning_in_session = resolve('session')->getSegment('eherkenning')->get('kvk');

		$field_type_digid = 'digid';
		$field_type_eherkenning = 'eherkenning';

		// Check if the form contains the eHerkenning field.
		$contains_field = false;
		foreach ($form['fields'] as $form_field) {
			if ($form_field->type == $field_type_eherkenning) {
				$contains_field = true;
				break;
			}
		}

		// If eHerkenning is in the session and the form contains the field, DigiD is optional.
		if ($eherkenning_in_session && $contains_field) {
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
