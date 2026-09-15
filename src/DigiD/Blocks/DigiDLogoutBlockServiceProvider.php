<?php

declare(strict_types=1);

/**
 * Service provider for the DigiD logout block.
 *
 * @since NEXT
 */

namespace Yard\DigiD\Blocks;

use function Yard\DigiD\Foundation\Helpers\config;
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
            return view('digid/logoutButton.php', ['logo' => $logo, 'title' => 'Uitloggen']);
        }

        if (! apply_filters('owc_digid_is_logged_in', false)) {
            return view('digid/logoutButton.php', ['logo' => $logo, 'title' => 'U bent niet ingelogd']);
        }

        return view('digid/logoutButton.php', [
            'logo' => $logo,
            'title' => 'Uitloggen',
            'link' => config('digid.url.logout'),
        ]);
    }
}
