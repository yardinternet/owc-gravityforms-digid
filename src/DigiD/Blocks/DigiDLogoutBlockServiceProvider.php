<?php

declare(strict_types=1);

/**
 * Service provider for the DigiD logout block.
 *
 * @since NEXT
 */

namespace Yard\DigiD\Blocks;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\Traits\BlockEditor;

/**
 * Service provider for the DigiD logout block.
 *
 * @since NEXT
 */
class DigiDLogoutBlockServiceProvider extends ServiceProvider
{
    use BlockEditor;

    private const EDITOR_SCRIPT_HANDLE = 'gravityforms-digid-logout-block-editor';
    private const EDITOR_STYLE_HANDLE = 'gravityforms-digid-logout-block-editor';

    public function register(): void
    {
        add_action('init', $this->registerBlock(...));
    }

    public function registerBlock(): void
    {
        wp_register_script(
            self::EDITOR_SCRIPT_HANDLE,
            Plugin::getInstance()->resourceUrl('digid-logout.js', 'blocks/dist'),
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render'],
            Plugin::VERSION
        );

        wp_register_style(
            self::EDITOR_STYLE_HANDLE,
            Plugin::getInstance()->resourceUrl('owc-gf-digid.css', 'css'),
            [],
            Plugin::VERSION
        );

        register_block_type_from_metadata(GF_DIGID_ROOT_PATH . '/resources/blocks/digid-logout', [
            'render_callback' => $this->render(...),
        ]);
    }

    public function render(): string
    {
        $logo = Plugin::getInstance()->resourceUrl('logo-digid.png', 'img');

        if ($this->isBlockEditor()) {
            return view('digid/logoutButton.php', ['logo' => $logo, 'title' => __('Log out', config('core.text_domain'))]);
        }

        if (! apply_filters('owc_digid_is_logged_in', false)) {
            return view('digid/logoutButton.php', ['logo' => $logo, 'title' => __('You are not logged in', config('core.text_domain'))]);
        }

        $this->storeResumeLink();

        return view('digid/logoutButton.php', [
            'logo' => $logo,
            'title' => __('Log out', config('core.text_domain')),
            'link' => config('digid.url.logout'),
        ]);
    }

    /**
     * Store the current page so DigiDController::redirectTo() can return the
     * visitor here after logout, since this block has no form/resume-token
     * context to derive that from.
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
}
