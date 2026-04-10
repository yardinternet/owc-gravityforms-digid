<?php

namespace Yard\DigiD\Binding;

use GoGentoOSS\SAMLBase\Binding\Artifact as BindingArtifact;

use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\view;

/**
 * Class Redirect
 *
 * POST binding that uses HTTP-POST as a transport for a SAML request
 *
 * @package GoGentoOSS\SAMLBase\Binding
 */
class Artifact extends BindingArtifact
{
    /**
     * Do a request with the current binding
     *
     * @param string $artifact
     * @return string
     */
    public function resolveArtifact($artifact = '')
    {
        $this->setTargetUrlFromMetadata($this->metadataBindingLocation);
        $this->setProtocolBinding(self::BINDING_POST);

        $this->getSettings()->setValue('artifact', $artifact);

        $soapRequest = $this->buildEnvelope('ArtifactResolve');
        $options = [
            'cert'    => config('digid.certificate.public'),
            'ssl_key' => config('digid.certificate.private'),
            'body'    => $soapRequest,
        ];

        if ($root = config('digid.certificate.root')) {
            $options['verify'] = $root;
        }

        $response = $this->getHttpService()->post($this->getTargetUrl(), $options);

        /** @var \GuzzleHttp\Psr7\Response $response */
        return (string) $response->getBody()->getContents();
    }

    protected function buildEnvelope($requestType = 'ArtifactResolve')
    {
        $requestTemplate = view(
            'digid/xml/' . $requestType . '.php',
            array_merge($this->getSettings()->getValues(), [
                'ProtocolBinding' => $this->getProtocolBinding(),
                'UniqueID'        => $this->getUniqueIdService()->generate(),
                'Timestamp'       => $this->getTimestampService()->generate()->toFormat(),
            ])
        );

        $signedTemplate = $this->signTemplate($requestTemplate);

        $signedTemplate = str_replace('<?xml version="1.0"?>', '', $signedTemplate);
        return view(
            'digid/xml/SoapEnvelope.php',
            ['SAMLContent' => $signedTemplate]
        );
    }
}
