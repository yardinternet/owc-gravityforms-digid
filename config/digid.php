<?php

use Yard\DigiD\GravityFormsSettings;

return [
    'issuer'        => GravityFormsSettings::make()->get('issuer'),
    'organization'  => [
        'name'        => GravityFormsSettings::make()->get('organization-name'),
        'displayName' => GravityFormsSettings::make()->get('organization-name'),
        'url'         => GravityFormsSettings::make()->get('organization-url'),
    ],
    'session' => [
        'lifetime'        => GravityFormsSettings::make()->get('lifetime'),
        'logout_wp_user'  => GravityFormsSettings::make()->get('logout-wp-user'),
    ],
    'url'    => [
        'idp'         => [
            'metadata' => GravityFormsSettings::make()->get('ipd-metadata-url')
        ],
        'acs'         => \site_url('/digid/acs'),
        'logged_out'  => \site_url('/digid/logged_out'),
        'logout'      => \site_url('/digid/logout'),
    ],
    'certificate' => [
        'public'  => GravityFormsSettings::make()->get('public-certificate'),
        'private' => GravityFormsSettings::make()->get('private-certificate')
    ]
];
