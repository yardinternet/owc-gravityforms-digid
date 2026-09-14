<?php

declare(strict_types=1);

namespace Yard\DigiD\Blocks;

use Exception;
use WP_Block_Editor_Context;
use Yard\DigiD\DigiDController;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\env;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\Traits\Logger;

class DigiDBlockServiceProvider extends ServiceProvider
{
    use Logger;

    private const BLOCK_CATEGORY = 'owc-gravityforms-digid';
    private const EDITOR_SCRIPT_HANDLE = 'gravityforms-digid-block-editor';
    private const EDITOR_STYLE_HANDLE = 'gravityforms-digid-block-editor';

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
            'render_callback' => [$this, 'render'],
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
        $fakeSession = env('DIGID_FAKE_SESSION') ?? '';

        if (! $this->hasCertificates() && empty($fakeSession)) {
            return view('digid/no-certificates.php');
        }

        if (apply_filters('owc_digid_is_logged_in', false)) {
            return view('digid/loggedin.php');
        }

        return $this->renderLoginButton($fakeSession);
    }

    private function renderLoginButton(string $fakeSession): string
    {
        try {
            $link = empty($fakeSession) ? DigiDController::getAuthNRequestURL() : '/digid/fake_login';
        } catch (Exception $e) {
            $this->logException($e, ['method' => __METHOD__]);

            return view('digid/no-certificates.php');
        }

        return view('digid/digidField.php', [
            'error' => resolve('session')->getSegment('digid')->getFlash('error'),
            'logo' => Plugin::getInstance()->resourceUrl('logo-digid.png', 'img'),
            'link' => $link,
            'title' => apply_filters('owc_gravityforms_digid_field_display_title', __('Login to', config('core.text_domain'))),
            'subtitle' => apply_filters('owc_gravityforms_digid_field_display_subtitle', get_bloginfo('name')),
        ]);
    }

    private function hasCertificates(): bool
    {
        return file_exists(config('digid.certificate.public')) || file_exists(config('digid.certificate.private'));
    }
}
