<?php

namespace Yard\DigiD;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use Wizkunde\SAMLBase\Metadata\ResolveService;
use Yard\DigiD\Binding\Artifact;
use Yard\DigiD\Binding\Redirect;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\make;
use function Yard\DigiD\Foundation\Helpers\resolve;
use Yard\DigiD\Foundation\Plugin;
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
        $gravityForm = new GravityForms;
        $this->plugin->getLoader()->addAction('gform_loaded', $gravityForm, 'registerField', 5);
        $this->registerSettingsAddon();


        if (\is_admin()) {
            return;
        }

        $this->plugin->getLoader()->addAction('wp_enqueue_scripts', $this, 'loadAssets');

        $this->plugin->getLoader()->addFilter('gform_pre_render', $gravityForm, 'clearFormOnFirstRender', 10, 1);
        $this->plugin->getLoader()->addFilter('gform_form_tag', $gravityForm, 'addCountDownHTML', 10, 2);
        $this->plugin->getLoader()->addAction('gform_after_submission', $gravityForm, 'clearFormAfterSubmission', 10, 2);

        $this->loadResolvers();

        $controller = resolve(\Yard\DigiD\DigiDController::class);
        resolve('route')->get('/digid/acs', [$controller, 'acsResolve']);
        resolve('route')->get('/digid/logged_out', [$controller, 'loggedOut']);
        resolve('route')->get('/digid/logout', [$controller, 'logOut']);
        resolve('route')->get('/digid/metadata', [$controller, 'metadata']);
    }

    /**
     * Load the public assets.
     *
     * @return void
     */
    public function loadAssets(): void
    {
        \wp_register_script('gravityforms_digid', Plugin::getInstance()->resourceUrl('app.js', 'js/dist'), [], Plugin::VERSION);
        \wp_enqueue_script('gravityforms_digid');
    }

    private function registerSettingsAddon(): void
    {
        if (!method_exists('\GFForms', 'include_addon_framework')) {
            return;
        }

        \GFForms::include_addon_framework();
        \GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }

    /**
     * Load all the dependencies.
     *
     * @return void
     */
    private function loadResolvers(): void
    {
        make('yard::guzzle-http', function () {
            return new \GuzzleHttp\Client([
                'cert'    => config('digid.certificate.public'),
                'ssl_key' => config('digid.certificate.private'),
            ]);
        });

        make('yard::digid:signing-certificate', function () {
            $certificate = new \Wizkunde\SAMLBase\Certificate();
            $certificate->setPublicKey(config('digid.certificate.public'), true);
            $certificate->setPrivateKey(config('digid.certificate.private'), true);
            return $certificate;
        });

        make('yard::digid::signature', function () {
            $signature = new \Wizkunde\SAMLBase\Security\Signature();
            $signature->setSigningAlgorithm(XMLSecurityDSig::SHA1);
            $signature->setCertificate(resolve('yard::digid:signing-certificate'));
            return $signature;
        });

        make('\Wizkunde\SAMLBase\Metadata\ResolveService', function () {
            return new ResolveService();
        });

        make('yard::digid::idp-settings', function () {
            $metaData = $this->getMetadata();
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
                    'Destination'            => $metaData['SingleSignOnServiceRedirect']['Location'],
                    'ArtifactResolve'        => $metaData['ArtifactResolutionService']['Location']
                ]);
        });

        make('yard::digid::redirect-binding', function () {
            $redirect = new Redirect;
            $redirect->setMetadata($this->getMetadata());
            $redirect->setUniqueIdService(resolve('\Wizkunde\SAMLBase\Configuration\UniqueID'));
            $redirect->setTimestampService(resolve('\Wizkunde\SAMLBase\Configuration\Timestamp'));
            $redirect->setSignatureService(resolve('yard::digid::signature'));
            $redirect->setSettings(resolve('yard::digid::idp-settings'));
            $redirect->setHttpService(resolve('yard::guzzle-http'));
            return $redirect;
        });

        make('yard::digid:artifact-binding', function () {
            $artifact = new Artifact;
            $artifact->setMetadata($this->getMetadata());
            $artifact->setUniqueIdService(resolve('\Wizkunde\SAMLBase\Configuration\UniqueID'));
            $artifact->setTimestampService(resolve('\Wizkunde\SAMLBase\Configuration\Timestamp'));
            $artifact->setSignatureService(resolve('yard::digid::signature'));
            $artifact->setSettings(resolve('yard::digid::idp-settings'));
            $artifact->setHttpService(resolve('yard::guzzle-http'));
            return $artifact;
        });
    }

    private function getMetadata()
    {
        $metadataKey =  sprintf('%s-%s', 'digid::metadata', md5(config('digid.url.idp.metadata')));
        if (false === ($metadata = get_transient($metadataKey))) {
            $metadata = resolve('\Wizkunde\SAMLBase\Metadata\ResolveService')->resolve(resolve('\Wizkunde\SAMLBase\Metadata\IDPMetadata'), config('digid.url.idp.metadata'));
            set_transient($metadataKey, $metadata, 12 * HOUR_IN_SECONDS);
        }
        return $metadata;
    }
}
