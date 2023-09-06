<?php

namespace Yard\DigiD;

use InvalidArgumentException;
use Yard\DigiD\Claim\Attributes;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\encrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDController
{
	/**
     * Add Content Security Policy (CSP) headers to the response.
	 *
	 * @since 1.1.4
     */
	private function addContentSecurityPolicyHeaders(): void
    {
        $cspDirectives = [
            'default-src' => 'self',
            'script-src' => 'self',
            'style-src' => 'self',
            'img-src' => 'self',
            'style-src' => 'self',
        ];

        $cspHeader = 'Content-Security-Policy: ';

        foreach ($cspDirectives as $directive => $value) {
            $cspHeader .= sprintf("%s '%s'; ", $directive, $value);
        }

        header($cspHeader);
    }

    /**
     * Handle the request of the ArtifactServiceLocation.
     *
     * @return void
     */
    public function acsResolve()
    {
		$this->addContentSecurityPolicyHeaders();

        if (!isset($_GET['SAMLart'])) {
            return $this->redirectTo();
        }

        /** @var \Aura\Session\Segment $session */
        $session = resolve('session')->getSegment('digid');

        try {
            $responseData = resolve('yard::digid:artifact-binding')
                ->resolveArtifact(\esc_attr($_REQUEST['SAMLart']));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $session->setFlash('error', \__('Something went wrong. Please try again.', config('core.text_domain')));
            return $this->redirectTo();
        }

        try {
            $attributes = new Attributes($responseData);
        } catch (InvalidArgumentException $e) {
            $session->setFlash('error', \__('Something went wrong. Please try again.', config('core.text_domain')));
            resolve('teams')->info('InvalidArgumentException', [
                'repsonseData' => $responseData,
                'attributes'   => $attributes,
                'resume_link'  => $session->get('resume_link'),
                'message'      => $session->get('message'),
                'exception'    => $e->getMessage()
            ]);
            return $this->redirectTo();
        }

        $session->set('session_id', $attributes->sessionID());
        $session->set('status_code', $attributes->status()->get()->getStatusCode());
        if ($attributes->status()->get()->isSuccess()) {
            $session->set('bsn', encrypt($attributes->bsn()->getID()));
            $session->set('nameID', encrypt($attributes->bsn()->getNameID()));
        } else {
            $session->set('bsn', '');
            $session->set('nameID', '');
            $session->setFlash('error', $attributes->status()->get()->message());
        }

        resolve('teams')->info('Attributes are filled', [
            'session_id'   => $attributes->sessionID(),
            'status_code'  => $attributes->status()->get()->getStatusCode(),
            'bsn'          => encrypt($attributes->bsn()->getID()),
            'nameID'       => encrypt($attributes->bsn()->getNameID()),
            'resume_link'  => $session->get('resume_link'),
            'message'      => $session->get('message')
        ]);

        return $this->redirectTo();
    }

    /**
     * Redirect to resume_link url in session.
     *
     * @return void
     */
    protected function redirectTo(): void
    {
        $session = resolve('session')->getSegment('digid');
        if (empty($session->get('resume_link'))) {
            $url = \site_url('/');
        } else {
            $url = $session->get('resume_link');
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Handle the logged out response of the IDP.
     *
     * @return void
     */
    public function loggedOut()
    {
        $SAMLResponse = (isset($_POST['SAMLResponse'])) ?  esc_attr($_POST['SAMLResponse']) : esc_attr($_GET['SAMLResponse']);
        $responseData = @gzinflate(base64_decode($SAMLResponse));
        $attributes = new Attributes($responseData);
        $session = resolve('session')->getSegment('digid');
        if ($attributes->logout()->get()->isSuccess()) {
            $session->set('bsn', '');
            $session->set('nameID', '');
            resolve('teams')->info('Customer is logged out', [
                'session_id'   => $attributes->sessionID(),
                'status_code'  => $attributes->logout()->get()->getStatusCode(),
                'bsn'          => $attributes->bsn()->getID(),
                'nameID'       => $attributes->bsn()->getNameID(),
                'resume_link'  => $session->get('resume_link')
            ]);
        }

        return $this->redirectTo();
    }

    /**
     * Send the logout request to the IDP.
     *
     * @return void
     */
    public function logOut()
    {
        if (null === ($nameID = decrypt(resolve('session')->getSegment('digid')->get('nameID', null)))) {
            return $this->redirectTo();
        }

        $settings = resolve('yard::digid::idp-settings');
        $settings->setValue('NameID', $nameID);

        $redirectUrl = resolve('yard::digid::redirect-binding')
            ->setSettings($settings)
            ->getURL('LogoutRequest');

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Generate the metadata required.
     *
     * @return string
     */
    public function metadata(): string
    {
        header('Content-Type: application/xml');
        echo DigiDMetadata::make()->toXML();
        exit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public static function getAuthNRequestURL(): string
    {
        $httpResponse = new \Symfony\Component\HttpFoundation\RedirectResponse(resolve('yard::digid::redirect-binding')
            ->getURL());
        return $httpResponse->getTargetUrl();
    }
}
