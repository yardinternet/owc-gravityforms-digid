<?php

use Yard\DigiD\GravityForms\GravityFormsSettings;

return [
    'issuer'        => GravityFormsSettings::make()->get('issuer'),
    'organization'  => [
        'name'        => GravityFormsSettings::make()->get('organization-name'),
        'displayName' => GravityFormsSettings::make()->get('organization-name'),
        'url'         => GravityFormsSettings::make()->get('organization-url'),
    ],
    'url'    => [
        'idp'         => [
            'metadata' => GravityFormsSettings::make()->get('ipd-metadata-url')
        ],
        'acs'         => '/digid/acs',
        'logged_out'  => '/digid/logged_out',
        'logout'      => '/digid/logout',
    ],
    'certificate' => [
        'public'  => GravityFormsSettings::make()->get('public-certificate'),
        'private' => GravityFormsSettings::make()->get('private-certificate')
    ]
];
