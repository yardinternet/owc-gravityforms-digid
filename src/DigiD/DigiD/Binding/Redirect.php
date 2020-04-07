<?php

namespace Yard\DigiD\DigiD\Binding;

use Wizkunde\SAMLBase\Binding\BindingAbstract;

/**
 * Class Redirect
 *
 * Redirect binding that uses HTTP-GET as a transport for a SAML request
 *
 * @package Wizkunde\SAMLBase\Binding
 */
class Redirect extends BindingAbstract
{
    /**
     * The location in the metadata that has the current bindings information
     * @var string
     */
    protected $metadataBindingLocation = 'SingleSignOnServiceRedirect';
    protected $metadataSLOLocation     = 'SingleLogoutServiceRedirect';

    /**
     * @param string $requestType
     * @return string
     */
    public function buildRequest($requestType = 'AuthnRequest')
    {
        $requestTemplate = $this->getTwigService()->render(
            $requestType . '.xml.twig',
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
            $signedURI .= '&SigAlg='.urlencode($key->type);
            $signature = $key->signData($signedURI);
            $signedURI .= '&Signature='.urlencode(base64_encode($signature));
        }

        $separator = (0 < strpos((string)$this->buildRequestUrl(), '?')) ? '&' : '?';
        return (string)$this->buildRequestUrl() . $separator . $signedURI;
    }
}
