<?php

declare(strict_types=1);

namespace Yard\DigiD;

use GFAddOn;
use GFForms;
use GoGentoOSS\SAMLBase\Metadata\ResolveService;
use OWC\IdpUserData\DigiDUserDataInterface;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use Yard\DigiD\Binding\Artifact;
use Yard\DigiD\Binding\Redirect;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\make;
use function Yard\DigiD\Foundation\Helpers\resolve;
use function Yard\DigiD\Foundation\Helpers\session_lifetime_in_seconds;
use function Yard\DigiD\Foundation\Helpers\view;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\Foundation\ServiceProvider;
use Yard\DigiD\UserData\DigiDUserData;

class DigiDServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $gravityForm = new GravityForms;
        $gravityForm->registerField();
        $this->registerSettingsAddon();

        if (is_admin()) {
            return;
        }

        $this->checkSession();

        add_action('wp_loaded', $this->registerRoutes(...));
        add_action('wp_enqueue_scripts', $this->loadAssets(...));
        add_action('wp_body_open', $this->addModalHTML(...));
        add_filter('gform_pre_render', $gravityForm->clearFormOnFirstRender(...));
        add_filter('gform_form_tag', $gravityForm->addCountDownHTML(...), 10, 2);
        add_filter('gform_field_validation', $gravityForm->optionalIDPs(...), 10, 4);
        add_filter('gform_submit_button', $gravityForm->handleSubmitIfLoginOnlyForm(...), 10, 2);
        add_action('gform_after_submission', $gravityForm->clearFormAfterSubmission(...), 10, 2);
        add_filter('owc_digid_is_logged_in', $this->setIsLoggedIn(...), 10, 1);
        add_filter('owc_digid_userdata', $this->setUserData(...), 10, 1);

        $this->loadResolvers();
    }

    public function registerRoutes(): void
    {
        $controller = resolve(\Yard\DigiD\DigiDController::class);

        resolve('route')->get('/digid/acs', [$controller, 'acsResolve']);
        resolve('route')->get('/digid/logged_out', [$controller, 'loggedOut']);
        resolve('route')->get('/digid/logout', [$controller, 'logOut']);
        resolve('route')->get('/digid/metadata', [$controller, 'metadata']);
        resolve('route')->get('/digid/fake_login', [$controller, 'fakeLogin']);
        resolve('route')->get('/digid/keep_alive', [$controller, 'keepAlive']);
    }

    public function setIsLoggedIn(bool $isLoggedIn): bool
    {
        if (! empty(resolve('session')->getSegment('digid')->get('bsn', ''))) {
            $isLoggedIn = true;
        }

        return $isLoggedIn;
    }

    public function setUserData(?DigiDUserDataInterface $userData): ?DigiDUserDataInterface
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn', '');

        if (! empty($bsn)) {
            $userData = new DigiDUserData(decrypt($bsn));
        }

        return $userData;
    }

    /**
     * Load the public assets.
     */
    public function loadAssets(): void
    {
        wp_register_script('gravityforms_digid', Plugin::getInstance()->resourceUrl('owc-gf-digid.js', 'js/dist'), [], Plugin::VERSION);

        $session = resolve('session')->getSegment('digid');

        if ($session->get('lastActivity')) {
            $digiDSession = new DigiDSession(session_lifetime_in_seconds());

            wp_add_inline_script(
                'gravityforms_digid',
                sprintf(
                    "document.addEventListener('DOMContentLoaded', function() {
						new CountdownDigiD.CountdownDigiD(%d, %d, '%s').init();
					});",
                    $digiDSession->getSessionLifeTime(),
                    $session->get('lastActivity'),
                    config('digid.url.logout')
                )
            );
        }

        wp_enqueue_script('gravityforms_digid');

        wp_register_style('gravityforms_digid', Plugin::getInstance()->resourceUrl('owc-gf-digid.css', 'css'), [], Plugin::VERSION);
        wp_enqueue_style('gravityforms_digid');
    }

    public function addModalHTML(): void
    {
        echo view('digid/modal.php', [ 'logoutLink' => config('digid.url.logout')]);
    }

    private function registerSettingsAddon(): void
    {
        if (! method_exists('\GFForms', 'include_addon_framework')) {
            return;
        }

        GFForms::include_addon_framework();
        GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }

    /**
     * Load all the dependencies.
     */
    private function loadResolvers(): void
    {
        make('yard::guzzle-http', function () {
            $options = [
                'cert' => config('digid.certificate.public'),
                'ssl_key' => config('digid.certificate.private'),
            ];

            $root = config('digid.certificate.root');

            if ('' !== $root && 'no-certificate' !== $root) {
                $options['verify'] = $root;
            }

            return new \GuzzleHttp\Client($options);
        });

        make('yard::digid:signing-certificate', function () {
            $certificate = new \GoGentoOSS\SAMLBase\Certificate();
            $certificate->setPublicKey(config('digid.certificate.public'), true);
            $certificate->setPrivateKey(config('digid.certificate.private'), true);

            return $certificate;
        });

        make('yard::digid::signature', function () {
            $signature = new \GoGentoOSS\SAMLBase\Security\Signature();
            $signature->setSigningAlgorithm(XMLSecurityDSig::SHA1);
            $signature->setCertificate(resolve('yard::digid:signing-certificate'));

            return $signature;
        });

        make('\GoGentoOSS\SAMLBase\Metadata\ResolveService', function () {
            $service = new ResolveService();
            $service->setClient(resolve('yard::guzzle-http'));

            return $service;
        });

        make('yard::digid::idp-settings', function () {
            $metaData = $this->getMetadata();

            return (new \GoGentoOSS\SAMLBase\Configuration\Settings())
                ->setValues([
                    'NameID' => config('digid.issuer'),
                    'Issuer' => config('digid.issuer'),
                    'MetadataExpirationTime' => 604800,
                    'SPReturnUrl' => config('digid.url.acs'),
                    'ForceAuthn' => 'false',
                    'IsPassive' => 'false',
                    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                    'ComparisonLevel' => 'minimum',
                    'Destination' => $metaData['SingleSignOnServiceRedirect']['Location'],
                    'ArtifactResolve' => $metaData['ArtifactResolutionService']['Location'],
                ]);
        });

        make('yard::digid::redirect-binding', function () {
            $redirect = new Redirect;
            $redirect->setMetadata($this->getMetadata());
            $redirect->setUniqueIdService(resolve('\GoGentoOSS\SAMLBase\Configuration\UniqueID'));
            $redirect->setTimestampService(resolve('\GoGentoOSS\SAMLBase\Configuration\Timestamp'));
            $redirect->setSignatureService(resolve('yard::digid::signature'));
            $redirect->setSettings(resolve('yard::digid::idp-settings'));
            $redirect->setHttpService(resolve('yard::guzzle-http'));

            return $redirect;
        });

        make('yard::digid:artifact-binding', function () {
            $artifact = new Artifact;
            $artifact->setMetadata($this->getMetadata());
            $artifact->setUniqueIdService(resolve('\GoGentoOSS\SAMLBase\Configuration\UniqueID'));
            $artifact->setTimestampService(resolve('\GoGentoOSS\SAMLBase\Configuration\Timestamp'));
            $artifact->setSignatureService(resolve('yard::digid::signature'));
            $artifact->setSettings(resolve('yard::digid::idp-settings'));
            $artifact->setHttpService(resolve('yard::guzzle-http'));

            return $artifact;
        });
    }

    private function getMetadata(): mixed
    {
        $metadataKey = sprintf('%s-%s', 'digid::metadata', md5(config('digid.url.idp.metadata')));

        if (false === ($metadata = get_transient($metadataKey))) {
            $metadata = resolve('\GoGentoOSS\SAMLBase\Metadata\ResolveService')->resolve(resolve('\GoGentoOSS\SAMLBase\Metadata\IDPMetadata'), config('digid.url.idp.metadata'));
            set_transient($metadataKey, $metadata, 12 * HOUR_IN_SECONDS);
        }

        return $metadata;
    }

    private function checkSession(): void
    {
        $session = resolve('session')->getSegment('digid');

        if (! $session->get('lastActivity')) {
            return;
        }

        $digiDSession = new DigiDSession(session_lifetime_in_seconds());

        if ($digiDSession->getSessionLifeTime() < (time() - $session->get('lastActivity'))) {
            $session->set('lastActivity', '');
            header('Location: ' . config('digid.url.logout'));

            exit;
        }

        $session->set('lastActivity', time());
    }
}
