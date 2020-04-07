<?php

namespace Yard\DigiD\DigiD;

use DOMDocument;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDMetadata
{
    /**
     * Static constructor
     *
     * @return self
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * Get the template file.
     *
     * @return string
     */
    protected function getTemplate(): string
    {
        return \file_get_contents(GF_DIGID_ROOT_PATH .'/views/metadata.php');
    }

    /**
     * Get all the necessary configurations.
     *
     * @return array
     */
    protected function getConfig(): array
    {
        return [
            'BaseURL'                  => config('digid.url.base'),
            'ACSURL'                   => config('digid.url.acs'),
            'ARSURL'                   => config('digid.url.ars'),
            'SLOURL'                   => config('digid.url.logout'),
            'SLOGGEDOUTURL'            => config('digid.url.logged_out'),
            'EntityID'                 => config('digid.issuer'),
            'ServiceProviderPublicKey' => resolve('SigningCertificate')->getPublicKey()->getX509Certificate(),
            'OrganizationName'         => config('digid.organization.name'),
            'OrganizationDisplayName'  => config('digid.organization.displayName'),
            'OrganizationURL'          => config('digid.organization.url'),
        ];
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
    protected function parseTemplate(): string
    {
        $variables = $this->getConfig();
        return preg_replace_callback(
            '#{\s?(.*?)\s?}#',
            function ($match) use ($variables) {
                $match[1] = trim($match[1], '$');
                return $variables[$match[1]];
            },
            ' ' . $this->getTemplate() . ' '
        );
    }

    /**
     * Build the metadata.
     *
     * @return DOMDocument
     */
    protected function buildMetadata(): DOMDocument
    {
        $document = new DOMDocument();
        $document->loadXML($this->parseTemplate());

        resolve('samlbase_signature')->signMetadata($document);

        return $document;
    }

    /**
     * Return the metadata as XML.
     *
     * @return string
     */
    public function toXML(): string
    {
        return $this->buildMetadata()->saveXML();
    }
}
