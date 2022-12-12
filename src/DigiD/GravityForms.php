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
        resolve('teams')->info('Isset BSN?', [
            'bsn'          => $bsn,
        ]);

        $logout = '';

        if (!empty($bsn)) {
            $digiDSession = new DigiDSession(config('digid.session.lifetime'), config('digid.session.resume-lifetime'));
            $logout = view('digid/logout.php', [
                'logoutLink'            => \site_url('/digid/logout'),
                'SessionLifeTime'       => $digiDSession->getSessionLifeTime(),
                'SessionResumeLifeTime' => $digiDSession->getSessionResumeLifeTime()
            ]);
        }

        $form_tag = str_replace("<form ", "<div id=\"js-countdown\" style=\"text-align:right; min-height: 30px;\"></div>". $logout ."<form ", $form_tag);

        return $form_tag;
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
