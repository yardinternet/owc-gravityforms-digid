<?php

namespace Yard\DigiD\DigiD;

use DateTime;
use DateTimeZone;
use DomAttr;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AbstractRequest;
use LightSaml\Model\Protocol\AuthnRequest as ProtocolAuthnRequest;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;
use RobRichards\XMLSecLibs\XMLSecurityKey;

use function Yard\DigiD\Foundation\Helpers\config;

/**
 * @deprecated
 */
class AuthnRequest extends ProtocolAuthnRequest
{
    /** @var RequestedAuthnContext */
    protected $requestedAuthnContext;

    /**
     * @param RequestedAuthnContext $requestedAuthnContext
     *
     * @return self
     */
    public function setRequestedAuthnContext(RequestedAuthnContext $requestedAuthnContext)
    {
        $this->requestedAuthnContext = $requestedAuthnContext;

        return $this;
    }

    /**
     * @return RequestedAuthnContext
     */
    public function getRequestedAuthnContextString(): RequestedAuthnContext
    {
        return $this->requestedAuthnContext;
    }

    public function get(): self
    {
        $this
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime("now", new DateTimeZone("UTC")))
            ->setIssuer(new Issuer(config('digid.issuer')))
            ->setDestination(config('digid.url.destination'))
            ->setAssertionConsumerServiceIndex('0')
            ->setRequestedAuthnContext(
                (new RequestedAuthnContext())
                ->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
                ->setAttributeNode(new DomAttr('Comparison', 'minimum'))
            )
            ->setSignature(
                $this->signature()
            );

        return $this;
    }

    protected function signature(): SignatureWriter
    {
        $certificate = X509Certificate::fromFile(\Yard\DigiD\Foundation\Helpers\config('digid.certificate.public'));
        $privateKey  = KeyHelper::createPrivateKey(\Yard\DigiD\Foundation\Helpers\config('digid.certificate.private'), '', true, XMLSecurityKey::RSA_SHA256);

        return new SignatureWriter($certificate, $privateKey);
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:AuthnRequest', SamlConstants::NS_PROTOCOL, $parent, $context);

        AbstractRequest::serialize($result, $context);

        $this->singleElementsToXml(['RequestedAuthnContext'], $result, $context);

        $this->attributesToXml([
                'ForceAuthn', 'IsPassive', 'ProtocolBinding', 'AssertionConsumerServiceIndex',
                'AssertionConsumerServiceURL', 'AttributeConsumingServiceIndex', 'ProviderName',
            ], $result);

        $this->singleElementsToXml(['Subject', 'NameIDPolicy', 'Conditions'], $result, $context);

        // must be last in order signature to include them all
        $this->singleElementsToXml(['Signature'], $result, $context);
    }
}
