<?php

namespace Yard\DigiD\DigiD;

use DOMDocument;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\view;

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
            'ServiceProviderPublicKey' => resolve('yard::digid:signing-certificate')->getPublicKey()->getX509Certificate(),
            'OrganizationName'         => config('digid.organization.name'),
            'OrganizationDisplayName'  => config('digid.organization.displayName'),
            'OrganizationURL'          => config('digid.organization.url'),
        ];
    }

    /**
     * Build the metadata.
     *
     * @return DOMDocument
     */
    protected function buildMetadata(): DOMDocument
    {
        $document = new DOMDocument();
        $document->loadXML(view('xml/metadata.php', $this->getConfig()));

        resolve('yard::digid::signature')->signMetadata($document);

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
