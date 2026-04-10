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
        'acs'         => \home_url('/digid/acs'),
        'logged_out'  => \home_url('/digid/logged_out'),
        'logout'      => \home_url('/digid/logout'),
    ],
    'certificate' => [
        'public'  => GravityFormsSettings::make()->get('public-certificate'),
        'private' => GravityFormsSettings::make()->get('private-certificate'),
        'root' => GravityFormsSettings::make()->get('root-certificate')
    ]
];
