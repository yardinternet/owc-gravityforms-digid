<?php

namespace Yard\DigiD\DigiD;

use Wizkunde\SAMLBase\Configuration\SessionID;

use Yard\DigiD\DigiD\Claim\Attributes;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDController
{
    /**
     * Generate the metadata required.
     *
     * @return void
     */
    public static function metadata(): void
    {
        $xml    = \file_get_contents(__DIR__ .'/views/metadata.php');
        $search = [
            'BaseURL'                                               => config('digid.url.base'),
            'ACSURL'                                                => config('digid.url.acs'),
            'ARSURL'                                                => config('digid.url.ars'),
            'SLOURL'                                                => config('digid.url.logout'),
            'SLOGGEDOUTURL'                                         => config('digid.url.logged_out'),
            'EntityID'                                              => config('digid.issuer'),
            'ServiceProviderPublicKey'                              => resolve('SigningCertificate')->getPublicKey()->getX509Certificate(),
            'OrganizationName'                                      => 'Gemeente Hoeksche Waard',
            'OrganizationDisplayName'                               => 'Gemeente Hoeksche Waard',
            'OrganizationURL'                                       => 'https://www.gemeentehw.nl/',
        ];

        $xml   = self::replaceVariablesInTemplate($xml, $search);

        $document = new \DOMDocument();
        $document->loadXML($xml);

        resolve('samlbase_signature')->signMetadata($document);

        header('Content-Type: application/xml');
        echo $document->saveXML();
        exit;
    }

    /**
     * Search and replace of variables.
     * Searching for ${VARIABLE}.
     *
     * @param string $template
     * @param array $variables
     *
     * @return string
     */
    protected static function replaceVariablesInTemplate($template, array $variables): string
    {
        return preg_replace_callback(
            '#{\s?(.*?)\s?}#',
            function ($match) use ($variables) {
                $match[1] = trim($match[1], '$');
                return $variables[$match[1]];
            },
            ' ' . $template . ' '
        );
    }

    /**
     * Handle the request of the ServiceLocationO.
     *
     * @return void
     */
    public static function acsResolve(): void
    {
        if (! isset($_GET['SAMLart'])) {
            header('Location: '. site_url('/'));
            exit;
        }

        $responseData = resolve('samlbase_binding_artifact')
            ->resolveArtifact($_REQUEST['SAMLart']);

        // $responseData = file_get_contents(\Yard\DigiD\Foundation\Helpers\storage_path('cert/DENIED.xml'));
        // $responseData = file_get_contents(\Yard\DigiD\Foundation\Helpers\storage_path('cert/SUCCESS.xml'));
        $attributes   = new Attributes($responseData);

        $session = resolve('session');
        $session->set('digid_session_id', (new SessionID())->getSessionIdFromDocument($responseData));
        $session->set('digid_status_code', $attributes->status()->get()->getStatusCode());
        if ($attributes->status()->get()->isSuccess()) {
            $session->set('digid_bsn', $attributes->bsn()->getID());
        } else {
            $session->set('digid_bsn', '');
        }

        if (empty($session->get('digid_resume_link'))) {
            header('Location: '. site_url('/'));
            exit;
        }

        header('Location: '. $session->get('digid_resume_link'));
        exit;
    }
}
