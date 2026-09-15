<?php

declare(strict_types=1);

/**
 * Service provider for the DigiD block.
 *
 * @since NEXT
 */

namespace Yard\DigiD\Blocks;

use Exception;
use WP_Block_Editor_Context;
use Yard\DigiD\DigiDController;
use Yard\DigiD\Fields\DigiDLoginField;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\env;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\Traits\BlockEditor;
use Yard\DigiD\Traits\Logger;

/**
 * Service provider for the DigiD block.
 *
 * @since NEXT
 */
class DigiDBlockServiceProvider extends ServiceProvider
{
    use BlockEditor;
    use Logger;

    private const BLOCK_CATEGORY = 'owc-gravityforms-digid';
    private const EDITOR_SCRIPT_HANDLE = 'gravityforms-digid-block-editor';
    private const EDITOR_STYLE_HANDLE = 'gravityforms-digid-block-editor-style';

    public function register(): void
    {
        add_action('init', $this->registerBlock(...));
        add_filter('block_categories_all', $this->registerBlockCategory(...), 10, 2);
    }

    public function registerBlock(): void
    {
        wp_register_script(
            self::EDITOR_SCRIPT_HANDLE,
            Plugin::getInstance()->resourceUrl('digid-login.js', 'blocks/dist'),
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render'],
            Plugin::VERSION
        );

        wp_register_style(
            self::EDITOR_STYLE_HANDLE,
            Plugin::getInstance()->resourceUrl('owc-gf-digid.css', 'css'),
            [],
            Plugin::VERSION
        );

        register_block_type_from_metadata(GF_DIGID_ROOT_PATH . '/resources/blocks/digid-login', [
            'render_callback' => $this->render(...)
        ]);
    }

    public function registerBlockCategory(array $categories, WP_Block_Editor_Context $context): array
    {
        $categories[] = [
            'slug' => self::BLOCK_CATEGORY,
            'title' => __('DigiD', config('core.text_domain')),
        ];

        return $categories;
    }

    public function render(): string
    {
        if ($this->isBlockEditor()) {
            return $this->renderPreview();
        }

        $fakeSession = trim((string) env('DIGID_FAKE_SESSION', ''));

        if (! $this->hasCertificates() && '' === $fakeSession) {
            return view('digid/no-certificates.php');
        }

        if (apply_filters('owc_digid_is_logged_in', false)) {
            return view('digid/loggedinBlock.php', [
                'logo' => Plugin::getInstance()->resourceUrl('logo-digid.png', 'img')
            ]);
        }

        return $this->renderLoginButton($fakeSession);
    }

    /**
     * ServerSideRender previews this block through a REST request. Render a
     * non-actionable preview here rather than reaching renderLoginButton(),
     * which would store the REST renderer URL as resume_link and generate a
     * real signed DigiD AuthnRequest (and touch certificates/metadata) on
     * every keystroke in the editor.
	 *
	 * @since NEXT
     */
    private function renderPreview(): string
    {
        return view('digid/digidField.php', [
            'logo' => Plugin::getInstance()->resourceUrl('logo-digid.png', 'img'),
            'link' => '',
            'title' => DigiDLoginField::getFieldTitle(),
            'subtitle' => DigiDLoginField::getFieldSubTitle(),
        ]);
    }

    private function renderLoginButton(string $fakeSession): string
    {
        $this->storeResumeLink();

        try {
            $link = '' === $fakeSession ? DigiDController::getAuthNRequestURL() : home_url('/digid/fake_login');
        } catch (Exception $e) {
            $this->logException($e, ['method' => __METHOD__]);

            return view('digid/error.php');
        }

        return view('digid/digidField.php', [
            'error' => resolve('session')->getSegment('digid')->getFlash('error'),
            'logo' => Plugin::getInstance()->resourceUrl('logo-digid.png', 'img'),
            'link' => $link,
            'title' => DigiDLoginField::getFieldTitle(),
            'subtitle' => DigiDLoginField::getFieldSubTitle(),
        ]);
    }

    /**
     * Store the current page so DigiDController::redirectTo() can return the
     * visitor here after login, since this block (unlike the Gravity Forms
     * field) has no form/resume-token context to derive that from.
     */
    private function storeResumeLink(): void
    {
        // REQUEST_URI already includes any multisite subdirectory path, so it
        // is appended to the trusted configured host directly rather than via
        // home_url(), which would duplicate that path segment.
        $host = wp_parse_url(home_url(), PHP_URL_HOST);
        $currentUrl = (is_ssl() ? 'https' : 'http') . '://' . $host . wp_unslash($_SERVER['REQUEST_URI'] ?? '/');

        resolve('session')->getSegment('digid')->set('resume_link', esc_url_raw($currentUrl));
    }

    private function hasCertificates(): bool
    {
        return file_exists(config('digid.certificate.public')) && file_exists(config('digid.certificate.private'));
    }
}
