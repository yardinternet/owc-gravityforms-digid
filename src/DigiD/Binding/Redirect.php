<?php

namespace Yard\DigiD\Binding;

use GoGentoOSS\SAMLBase\Binding\BindingAbstract;

use function Yard\DigiD\Foundation\Helpers\view;

/**
 * Class Redirect
 *
 * Redirect binding that uses HTTP-GET as a transport for a SAML request
 *
 * @package GoGentoOSS\SAMLBase\Binding
 */
class Redirect extends BindingAbstract
{
    /**
     * The location in the metadata that has the current bindings information
     * @var string
     */
    protected $metadataBindingLocation = 'SingleSignOnServiceRedirect';
    protected $metadataSLOLocation = 'SingleLogoutServiceRedirect';

    /**
     * @param string $requestType
     * @return string
     */
    public function buildRequest($requestType = 'AuthnRequest')
    {
        $requestTemplate = view(
            'digid/xml/' . $requestType . '.php',
            array_merge($this->getSettings()->getValues(), [
                'ProtocolBinding' => $this->getProtocolBinding(),
                'UniqueID'        => $this->getUniqueIdService()->generate(),
                'Timestamp'       => $this->getTimestampService()->generate()->toFormat(),
                'Destination'     => $this->getTargetUrl()
            ])
        );

        return $this->prepareTemplateForRequest($requestTemplate);
    }

    /**
     * Do a request with the current binding
     */
    public function getURL($requestType = 'AuthnRequest', $relayState = ''): string
    {
        parent::request($requestType);

        $this->setProtocolBinding(self::BINDING_REDIRECT);

        $signedURI = 'SAMLRequest=' . $this->buildRequest($requestType);
        /** @var $key XMLSecurityKey */
        $key = $this->getSignatureService() ? $this->getSignatureService()->getCertificate()->getPrivateKey() : null;
        if (null != $key) {
            $signedURI .= '&SigAlg=' . urlencode($key->type);
            $signature = $key->signData($signedURI);
            $signedURI .= '&Signature=' . urlencode(base64_encode($signature));
        }

        $separator = (0 < strpos((string) $this->buildRequestUrl(), '?')) ? '&' : '?';
        return (string) $this->buildRequestUrl() . $separator . $signedURI;
    }
}
