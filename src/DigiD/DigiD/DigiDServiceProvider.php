<?php

namespace Yard\DigiD\DigiD;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use Wizkunde\SAMLBase\Metadata\ResolveService;
use Yard\DigiD\DigiD\Binding\Artifact;
use Yard\DigiD\DigiD\Binding\Redirect;
use Yard\DigiD\Foundation\ServiceProvider;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\make;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->loadResolvers();

        $controller = resolve(\Yard\DigiD\DigiD\DigiDController::class);
        resolve('route')->get('/digid/acs', [$controller, 'acsResolve']);
        resolve('route')->get('/digid/logged_out', [$controller, 'loggedOut']);
        resolve('route')->get('/digid/logout', [$controller, 'logOut']);
        resolve('route')->get('/digid/metadata', [$controller, 'metadata']);
    }

    /**
     * Load all the dependencies.
     *
     * @return void
     */
    private function loadResolvers(): void
    {
        make('digid', function () {
            return new DigiD(new AuthnRequest);
        });

        make('twig_loader', function () {
            return new \Twig_Loader_Filesystem(GF_DIGID_ROOT_PATH .'/views');
        });

        make('twig', function () {
            return new \Twig_Environment(resolve('twig_loader'));
        });

        make('guzzle_http', function () {
            return new \GuzzleHttp\Client([
                'cert'    => config('digid.certificate.public'),
                'ssl_key' => config('digid.certificate.private'),
            ]);
        });

        make('SigningCertificate', function () {
            $certificate = new \Wizkunde\SAMLBase\Certificate();
            $certificate->setPublicKey(config('digid.certificate.public'), true);
            $certificate->setPrivateKey(config('digid.certificate.private'), true);
            return $certificate;
        });

        make('EncryptionCertificate', function () {
            $certificate = new \Wizkunde\SAMLBase\Certificate();
            $certificate->setPublicKey(config('digid.certificate.public'), true);
            $certificate->setPrivateKey(config('digid.certificate.private'), true);
            return $certificate;
        });

        make('samlbase_encryption', function () {
            return (new \Wizkunde\SAMLBase\Security\Encryption())
                ->setCertificate(
                    resolve('EncryptionCertificate')
                );
        });

        make('samlbase_signature', function () {
            $signature = new \Wizkunde\SAMLBase\Security\Signature();
            $signature->setSigningAlgorithm(XMLSecurityDSig::SHA1);
            $signature->setCertificate(resolve('SigningCertificate'));
            return $signature;
        });

        make('resolver', function () {
            return new ResolveService(resolve('guzzle_http'));
        });

        make('samlbase_idp_settings', function () {
            return (new \Wizkunde\SAMLBase\Configuration\Settings())
                ->setValues([
                    'NameID'                 => config('digid.issuer'),
                    'Issuer'                 => config('digid.issuer'),
                    'MetadataExpirationTime' => 604800,
                    'SPReturnUrl'            => config('digid.url.acs'),
                    'ForceAuthn'             => 'false',
                    'IsPassive'              => 'false',
                    'NameIDFormat'           => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                    'ComparisonLevel'        => 'minimum',
                    'Destination'            => config('digid.url.destination')
                ]);
        });

        make('samlbase_binding_redirect', function () {
            $redirect = new Redirect;
            $redirect->setMetadata(resolve('resolver')->resolve(resolve('\Wizkunde\SAMLBase\Metadata\IDPMetadata'), config('digid.url.idp.metadata')));
            $redirect->setUniqueIdService(resolve('\Wizkunde\SAMLBase\Configuration\UniqueID'));
            $redirect->setTimestampService(resolve('\Wizkunde\SAMLBase\Configuration\Timestamp'));
            $redirect->setSignatureService(resolve('samlbase_signature'));
            $redirect->setSettings(resolve('samlbase_idp_settings'));
            $redirect->setTwigService(resolve('twig'));
            $redirect->setHttpService(resolve('guzzle_http'));
            return $redirect;
        });

        make('samlbase_binding_artifact', function () {
            $artifact = new Artifact;
            $artifact->setMetadata(resolve('resolver')->resolve(resolve('\Wizkunde\SAMLBase\Metadata\IDPMetadata'), config('digid.url.idp.metadata')));
            $artifact->setUniqueIdService(resolve('\Wizkunde\SAMLBase\Configuration\UniqueID'));
            $artifact->setTimestampService(resolve('\Wizkunde\SAMLBase\Configuration\Timestamp'));
            $artifact->setSignatureService(resolve('samlbase_signature'));
            $artifact->setSettings(resolve('samlbase_idp_settings'));
            $artifact->setTwigService(resolve('twig'));
            $artifact->setHttpService(resolve('guzzle_http'));
            return $artifact;
        });
    }
}
