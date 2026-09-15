<?php

declare(strict_types=1);

/**
 * Trait for checking if the current screen is the block editor.
 *
 * @since NEXT
 */

namespace Yard\DigiD\Traits;

use WP_Screen;

/**
 * Trait for checking if the current screen is the block editor.
 *
 * @since NEXT
 */
trait BlockEditor
{
    protected function isBlockEditor(): bool
    {
        global $current_screen;

        if ($current_screen instanceof WP_Screen
            && method_exists($current_screen, 'is_block_editor')
            && $current_screen->is_block_editor()
        ) {
            return true;
        }

        return defined('REST_REQUEST') && REST_REQUEST
            && 'edit' === ($_GET['context'] ?? '');
    }
}
