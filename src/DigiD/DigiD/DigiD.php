<?php

namespace Yard\DigiD\DigiD;

class DigiD
{
    // protected $authnRequest;

    // public function __construct(AuthnRequest $authnRequest)
    // {
    //     $this->authnRequest = $authnRequest;
    // }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function getAuthNRequestURL(): string
    {
        $redirectUrl = \Yard\DigiD\Foundation\Helpers\resolve('samlbase_binding_redirect')
            ->getURL();
        $httpResponse = new \Symfony\Component\HttpFoundation\RedirectResponse($redirectUrl);
        return $httpResponse->getTargetUrl();

        // $authnRequest    = $this->authnRequest->get();
        // $redirectBinding = (new \LightSaml\Binding\BindingFactory())->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        // $messageContext  = new \LightSaml\Context\Profile\MessageContext();
        // $messageContext->setMessage($authnRequest);

        // $serializationContext = new \LightSaml\Model\Context\SerializationContext();
        // $authnRequest->serialize($serializationContext->getDocument(), $serializationContext);
        // $serializationContext->getDocument()->formatOutput = true;
        // \Yard\BAG\Foundation\Helpers\dd($serializationContext->getDocument()->save('/app/test2.xml', LIBXML_NOBLANKS));

        // /** @var \Symfony\Component\HttpFoundation\RedirectResponse $httpResponse */
        // $httpResponse = $redirectBinding->send($messageContext);
        // return $httpResponse->getTargetUrl();
    }
}
