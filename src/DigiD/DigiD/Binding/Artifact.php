<?php

namespace Yard\DigiD\DigiD\Binding;

use Wizkunde\SAMLBase\Binding\Artifact as BindingArtifact;

use function Yard\DigiD\Foundation\Helpers\config;

/**
 * Class Redirect
 *
 * POST binding that uses HTTP-POST as a transport for a SAML request
 *
 * @package Wizkunde\SAMLBase\Binding
 */
class Artifact extends BindingArtifact
{
    /**
     * Do a request with the current binding
     */
    public function resolveArtifact($artifact = '')
    {
        $this->setTargetUrlFromMetadata($this->metadataBindingLocation);
        $this->setProtocolBinding(self::BINDING_POST);

        $this->getSettings()->setValue('artifact', $artifact);

        $soapRequest = $this->buildEnvelope('ArtifactResolve');
        $response    = $this->getHttpService()->post($this->getTargetUrl(), [
            'cert'    => config('digid.certificate.public'),
            'ssl_key' => config('digid.certificate.private'),
            'body'    => $soapRequest
        ]);

        /** @var \GuzzleHttp\Psr7\Response $response */
        return (string) $response->getBody()->getContents();
    }
}
