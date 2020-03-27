<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yard\DigiD\DigiD;

use DomAttr;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class RequestedAuthnContext extends AbstractSamlModel
{
    /**
     * @var string|null
     */
    protected $authnContextClassRef;

    /**
     * @var string|null
     */
    protected $authnContextDecl;

    /**
     * @var string|null
     */
    protected $authnContextDeclRef;

    /**
     * @var string|null
     */
    protected $authenticatingAuthority;

    /**
     * @var DomAttr;
     */
    protected $attributeNode;

    /**
     * @param string|null $authenticatingAuthority
     *
     * @return RequestedAuthnContext
     */
    public function setAuthenticatingAuthority($authenticatingAuthority)
    {
        $this->authenticatingAuthority = (string) $authenticatingAuthority;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticatingAuthority()
    {
        return $this->authenticatingAuthority;
    }

    /**
     * @param null|string $authnContextClassRef
     *
     * @return RequestedAuthnContext
     */
    public function setAuthnContextClassRef($authnContextClassRef)
    {
        $this->authnContextClassRef = (string) $authnContextClassRef;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }

    /**
     * @param null|string $authnContextDecl
     *
     * @return RequestedAuthnContext
     */
    public function setAuthnContextDecl($authnContextDecl)
    {
        $this->authnContextDecl = (string) $authnContextDecl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }

    /**
     * @param null|string $authnContextDeclRef
     *
     * @return RequestedAuthnContext
     */
    public function setAuthnContextDeclRef($authnContextDeclRef)
    {
        $this->authnContextDeclRef = (string) $authnContextDeclRef;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }

    public function getAttributeNode(): DomAttr
    {
        return $this->attributeNode;
    }

    public function setAttributeNode(DomAttr $attributeNode)
    {
        $this->attributeNode = $attributeNode;
        return $this;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:RequestedAuthnContext', null, $parent, $context);

        if ($this->getAttributeNode()) {
            $parent->setAttribute('xmlns:saml', SamlConstants::NS_ASSERTION);
            $result->setAttributeNode($this->getAttributeNode());
        }

        $this->singleElementsToXml(
            [
                'saml:AuthnContextClassRef',
                'AuthnContextDecl',
                'AuthnContextDeclRef',
                'AuthenticatingAuthority'
            ],
            $result,
            $context
        );
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AuthnContext', SamlConstants::NS_ASSERTION);

        $this->singleElementsFromXml($node, $context, [
            'AuthnContextClassRef'    => ['saml', null],
            'AuthnContextDecl'        => ['saml', null],
            'AuthnContextDeclRef'     => ['saml', null],
            'AuthenticatingAuthority' => ['saml', null],
        ]);
    }
}
