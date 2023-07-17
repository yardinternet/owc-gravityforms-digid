<?php

namespace Yard\DigiD\Fields;

use Aura\Session\Segment;
use Yard\DigiD\DigiD;
use Yard\DigiD\DigiDController;
use Yard\DigiD\Foundation\Plugin;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\encrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;

class DigiDLoginField extends AbstractField
{
    /** @var DigiD */
    protected $digid;

    /** @var Segment */
    protected $session;

    /**
     * @param object $field
     * @param array $value
     */
    public function __construct(object $field, array $value, Segment $session)
    {
        parent::__construct($field, $value);
        $this->session = $session;
    }

    /**
     * Render the input.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->is_admin || !\rgar($this->getInput(), 'isHidden')) {
            if (!\is_admin()) {
                $bsn = $this->session->get('bsn', '');

                if (!empty($bsn)) {
                    $bsn = encrypt($bsn);
                }

                if (!empty($bsn)) {
                    return view('digid/loggedin.php');
                }

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					$this->session->set('resume_link', $this->getResumeLink());

					resolve('teams')->info('Set resume_link', [
						'user_agent'                        => $_SERVER['HTTP_USER_AGENT'] ?? '',
						'resume_link_from_session'          => $this->session->get('resume_link'),
						'resume_link'                       => $this->getResumeLink(),
					]);
				}
            }

            return "{$this->getSpanField()}
                        {$this->getLabelField()}
                        {$this->getInputField()}
                    </span>";
        }

        return '';
    }

    /**
     * Get the resume link.
     *
     * @return string
     */
    protected function getResumeLink(): string
    {
        // If form is not yet created.
        if (1 > $this->field->formId) {
            return '';
        }

        if (\is_admin()) {
            return '';
        }

        add_filter('gform_incomplete_submission_pre_save', function ($submission_json, $resume_token, $form) {
            $submissionData = \json_decode($submission_json);
            $submissionData->page_number = \GFFormDisplay::get_current_page($this->field->formId);
            $submission_json = \json_encode($submissionData);
            return $submission_json;
        }, 10, 3);

        $resume = \GFAPI::submit_form(
            $this->field->formId,
            [
                'gf_submitting_' . $this->field->formId => true,
                'saved_for_later'                       => true,
                'gform_save'                            => true,
            ]
        );

        $resumeToken = $resume['resume_token'] ?? null;
        return sprintf('%s?gf_token=%s', \get_permalink(), $resumeToken);
    }

    /**
     * Get the structured label of the field.
     *
     * @return string
     */
    protected function getLabelField(): string
    {
        return "";
    }

    /**
     * Get the structured input.
     *
     * @return string
     */
    protected function getInputField(): string
    {
        return view('digid/digidField.php', [
            'error'    => $this->session->getFlash('error'),
            'logo'     => Plugin::getInstance()->resourceUrl('logo-digid.png', 'img'),
            'link'     => \is_admin() ? '' : DigiDController::getAuthNRequestURL(),
            'title'    => $this->getFieldTitle(),
            'subtitle' => $this->getFieldSubTitle()
        ]);
    }

    /**
     * Get the input field display title.
     *
     * @return string
     */
    protected function getFieldTitle(): string
    {
        return apply_filters('owc_gravityforms_digid_field_display_title', __('Login to', config('core.text_domain')));
    }

    /**
     * Get the input field display subtitle.
     *
     * @return string
     */
    protected function getFieldSubTitle(): string
    {
        return apply_filters('owc_gravityforms_digid_field_display_subtitle', get_bloginfo('name'));
    }
}
