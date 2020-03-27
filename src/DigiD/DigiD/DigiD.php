<?php

namespace Yard\DigiD\DigiD;

use LightSaml\Binding\BindingFactory;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\SamlConstants;

class DigiD
{
    protected $authnRequest;

    public function __construct(AuthnRequest $authnRequest)
    {
        $this->authnRequest = $authnRequest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function getAuthNRequestURL(): string
    {
        $authnRequest    = $this->authnRequest->get();
        $redirectBinding = (new BindingFactory())->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        $messageContext  = new MessageContext();
        $messageContext->setMessage($authnRequest);

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $httpResponse */
        $httpResponse = $redirectBinding->send($messageContext);
        return $httpResponse->getTargetUrl();
    }
}
