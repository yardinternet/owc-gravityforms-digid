<?php

return [
    /*
     * Service Providers.
     */
    'providers' => [
        Yard\DigiD\DigiDServiceProvider::class,
        Yard\DigiD\Blocks\DigiDBlockServiceProvider::class,
        Yard\DigiD\Blocks\DigiDLogoutBlockServiceProvider::class,
    ],

    'text_domain' => 'owc-gravityforms-digid',
];
