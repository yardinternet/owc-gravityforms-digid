<?php

namespace Yard\DigiD\DigiD;

use Yard\DigiD\DigiD\Claim\Attributes;

use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDController
{
    /**
     * Handle the request of the ArtifactServiceLocation.
     *
     * @return void
     */
    public function acsResolve()
    {
        if (! isset($_GET['SAMLart'])) {
            return $this->redirectTo();
        }

        /** @var \Aura\Session\Segment $session */
        $session = resolve('session')->getSegment('digid');

        try {
            $responseData = resolve('samlbase_binding_artifact')
                ->resolveArtifact($_REQUEST['SAMLart']);
        } catch (\GuzzleHttp\Exception\ClientException $e ) {
            $session->setFlash('error', __('Something gone wrong'));
            return $this->redirectTo();
        }

        // $responseData = file_get_contents(\Yard\DigiD\Foundation\Helpers\storage_path('cert/DENIED.xml'));
        // $responseData = file_get_contents(\Yard\DigiD\Foundation\Helpers\storage_path('cert/SUCCESS.xml'));
        $attributes   = new Attributes($responseData);

		$session->set('session_id', $attributes->sessionID());
        $session->set('status_code', $attributes->status()->get()->getStatusCode());
        if ($attributes->status()->get()->isSuccess()) {
            $session->set('bsn', $attributes->bsn()->getID());
            $session->set('nameID', $attributes->bsn()->getNameID());
        } else {
            $session->set('bsn', '');
            $session->set('nameID', '');
            $session->setFlash('error', 'Error occured!');
        }

        resolve('teams')->info('Attributes are filled', [
            'session_id'   => $attributes->sessionID(),
            'status_code'  => $attributes->status()->get()->getStatusCode(),
            'bsn'          => $attributes->bsn()->getID(),
            'nameID'       => $attributes->bsn()->getNameID(),
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
            $url = site_url('/');
        } else {
            $url = $session->get('resume_link');
        }

        header('Location: '. $url);
        exit;
    }

    /**
     * Handle the logged out response of the IDP.
     *
     * @return void
     */
    public function loggedOut()
    {
        $SAMLResponse = (isset($_POST['SAMLResponse'])) ?  $_POST['SAMLResponse'] : $_GET['SAMLResponse'];
        $responseData = @gzinflate(base64_decode($SAMLResponse));
        $attributes   = new Attributes($responseData);
        $session      = resolve('session')->getSegment('digid');
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
        if (null === ($nameID = resolve('session')->getSegment('digid')->get('nameID', null))) {
            return $this->redirectTo();
        }

        $settings = resolve('samlbase_idp_settings');
        $settings->setValue('NameID', $nameID);

        $redirectUrl = resolve('samlbase_binding_redirect')
            ->setSettings($settings)
            ->getURL('LogoutRequest');

        header('Location: ' .$redirectUrl);
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
}
