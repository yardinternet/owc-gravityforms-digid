<?php

declare(strict_types=1);

namespace Yard\DigiD\Blocks;

use Exception;
use Yard\DigiD\DigiDController;
use Yard\DigiD\Fields\DigiDLoginField;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\env;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\Traits\Logger;

/**
 * Registers the server-side rendered DigiD login and logout blocks.
 *
 * @since NEXT
 */
class BlockServiceProvider extends ServiceProvider
{
    use Logger;

    public function register(): void
    {
        add_action('init', $this->registerBlocks(...));
        add_filter('block_categories_all', $this->registerBlockCategory(...));
    }

    public function registerBlocks(): void
    {
        register_block_type(GF_DIGID_ROOT_PATH . '/build/digid-login', [
            'render_callback' => $this->renderLogin(...),
        ]);

        register_block_type(GF_DIGID_ROOT_PATH . '/build/digid-logout', [
            'render_callback' => $this->renderLogout(...),
        ]);
    }

    public function registerBlockCategory(array $categories): array
    {
        $categories[] = [
            'slug' => 'owc-gravityforms-digid',
            'title' => __('DigiD', config('core.text_domain')),
        ];

        return $categories;
    }

    public function renderLogin(): string
    {
        if ($this->isEditorPreview()) {
            return $this->wrap($this->loginButton(''));
        }

        $fakeSession = trim((string) env('DIGID_FAKE_SESSION', ''));

        if (! $this->hasCertificates() && '' === $fakeSession) {
            return $this->wrap(view('digid/no-certificates.php'));
        }

        if (apply_filters('owc_digid_is_logged_in', false)) {
            return $this->wrap(view('digid/loggedinBlock.php', ['logo' => $this->logo()]));
        }

        $this->storeResumeLink();

        try {
            $link = '' === $fakeSession ? DigiDController::getAuthNRequestURL() : home_url('/digid/fake_login');
        } catch (Exception $e) {
            $this->logException($e, ['method' => __METHOD__]);

            return $this->wrap(view('digid/error.php'));
        }

        return $this->wrap($this->loginButton($link, resolve('session')->getSegment('digid')->getFlash('error')));
    }

    public function renderLogout(): string
    {
        $logout = __('Log out', config('core.text_domain'));

        if ($this->isEditorPreview()) {
            return $this->wrap($this->button($logout));
        }

        if (! apply_filters('owc_digid_is_logged_in', false)) {
            return $this->wrap($this->button(__('You are not logged in', config('core.text_domain'))));
        }

        $this->storeResumeLink();

        return $this->wrap($this->button($logout, config('digid.url.logout')));
    }

    private function loginButton(string $link, ?string $error = null): string
    {
        return $this->button(DigiDLoginField::getFieldTitle(), $link, DigiDLoginField::getFieldSubTitle(), $error);
    }

    private function button(string $title, string $link = '', string $subtitle = '', ?string $error = null): string
    {
        return view('digid/digidField.php', [
            'error' => $error,
            'logo' => $this->logo(),
            'link' => $link,
            'title' => $title,
            'subtitle' => $subtitle,
        ]);
    }

    private function logo(): string
    {
        return Plugin::getInstance()->resourceUrl('logo-digid.png', 'img');
    }

    /**
     * Adds the wp-block-owc-gravityforms-digid-{login,logout} wrapper class;
     * the inner digid-btn classes stay as-is for themes that style them.
     */
    private function wrap(string $html): string
    {
        return sprintf('<div %s>%s</div>', get_block_wrapper_attributes(), $html);
    }

    /**
     * ServerSideRender previews the block through the REST block-renderer
     * endpoint. Render a non-actionable preview there instead of storing the
     * REST URL as resume_link and signing a real DigiD AuthnRequest on every
     * editor refresh.
     */
    private function isEditorPreview(): bool
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }

    /**
     * Store the current page so DigiDController::redirectTo() can return the
     * visitor here after login/logout, since a block (unlike the Gravity Forms
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
