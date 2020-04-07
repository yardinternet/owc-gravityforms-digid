<?php

use function \Yard\DigiD\Foundation\Helpers\storage_path;

return [
    'issuer'        => 'https://preprod-formulieren.gemeentehw.nl/digid',
    'organization'  => [
        'name'        => 'Gemeente Hoeksche Waard',
        'displayName' => 'Gemeente Hoeksche Waard',
        'url'	        => 'https://www.gemeentehw.nl/'
    ],
    'url'    => [
        'base'        => 'https://preprod-formulieren.gemeentehw.nl/',
        'metadata'    => 'https://preprod-formulieren.gemeentehw.nl/digid/metadata',
        'destination' => 'https://preprod1.digid.nl/saml/idp/request_authentication',
        'acs'         => 'https://preprod-formulieren.gemeentehw.nl/digid/acs',
        'ars'         => 'https://was-preprod1.digid.nl/saml/idp/resolve_artifact',
        'logged_out'  => 'https://preprod-formulieren.gemeentehw.nl/digid/logged_out',
        'logout'      => 'https://preprod-formulieren.gemeentehw.nl/digid/logout',
        'idp'         => [
            'metadata' => 'https://was-preprod1.digid.nl/saml/idp/metadata'
        ]
    ],
    'certificate' => [
        'public'  => storage_path('cert/preprod-formulieren.gemeentehw.nl.cer'),
        'private' => storage_path('cert/preprod-formulieren.gemeentehw.nl.key')
    ]
];
