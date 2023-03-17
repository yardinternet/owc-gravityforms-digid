<?php
if (!\function_exists('xdebug_set_filter')) {
    return;
}

\xdebug_set_filter(
    \XDEBUG_FILTER_CODE_COVERAGE,
    \XDEBUG_PATH_WHITELIST,
    [
        '/app/htdocs/wp-content/plugins/owc-gravityforms-digid/src/DigiD/'
    ]
);
