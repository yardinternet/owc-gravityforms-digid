<?php

namespace Yard\DigiD\DigiD;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use Wizkunde\SAMLBase\Metadata\ResolveService;
use Yard\DigiD\DigiD\Binding\Artifact;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\make;
use function Yard\DigiD\Foundation\Helpers\resolve;
use Yard\DigiD\Foundation\ServiceProvider;

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

        resolve('route')->get('/digid/metadata', ['\Yard\DigiD\DigiD\DigiDController', 'metadata']);
        resolve('route')->get('/digid/acs', ['\Yard\DigiD\DigiD\DigiDController', 'acsResolve']);
    }

    private function loadResolvers()
    {
        make('digid', function () {
            return new DigiD(new AuthnRequest);
        });

        make('twig_loader', function () {
            return new \Twig_Loader_Filesystem(__DIR__ .'/../DigiD/views');
        });

        make('twig', function () {
            return new \Twig_Environment(resolve('twig_loader'));
        });

        make('guzzle_http', function () {
            return new \GuzzleHttp\Client([
                'verify'  => false,
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

        make('samlbase_unique_id_generator', function () {
            return new \Wizkunde\SAMLBase\Configuration\UniqueID;
        });

        make('samlbase_timestamp_generator', function () {
            return new \Wizkunde\SAMLBase\Configuration\Timestamp;
        });

        make('samlbase_metadata', function () {
            return new \Wizkunde\SAMLBase\Metadata\IDPMetadata;
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
                ]);
        });

        make('samlbase_binding_artifact', function () {
            $artifact = new Artifact;
            $artifact->setMetadata(resolve('resolver')->resolve(resolve('samlbase_metadata'), config('digid.url.idp.metadata')));
            $artifact->setUniqueIdService(resolve('samlbase_unique_id_generator'));
            $artifact->setTimestampService(resolve('samlbase_timestamp_generator'));
            $artifact->setSignatureService(resolve('samlbase_signature'));
            $artifact->setSettings(resolve('samlbase_idp_settings'));
            $artifact->setTwigService(resolve('twig'));
            $artifact->setHttpService(resolve('guzzle_http'));
            return $artifact;
        });
    }
}
