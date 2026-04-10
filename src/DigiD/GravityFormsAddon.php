<?php

namespace Yard\DigiD;

use GFAddOn;
use function Yard\DigiD\Foundation\Helpers\config;
use function Yard\DigiD\Foundation\Helpers\storage_path;

class GravityFormsAddon extends GFAddOn
{
    /**
     * Version number.
     *
     * @var string
     */
    protected $_version = GF_DIGID_VERSION;

    /**
     * Minimal required GF version.
     *
     * @var string
     */
    protected $_min_gravityforms_version = '2.4';

    /**
     * Subview slug.
     *
     * @var string
     */
    protected $_slug = 'owc-gravityforms-digid';

    /**
     * Relative path to the plugin from the plugins folder.
     *
     * @var string
     */
    protected $_path = GF_DIGID_ROOT_PATH . '/plugin.php';

    /**
     * The physical path to the main plugin file.
     *
     * @var string
     */
    protected $_full_path = __FILE__;

    /**
     * The complete title of the Add-On.
     *
     * @var string
     */
    protected $_title = 'OWC GravityForms DigiD';

    /**
     * The short title of the Add-On to be used in limited spaces.
     *
     * @var string
     */
    protected $_short_title = 'OWC DigiD';

    /**
     * Instance object
     *
     * @var self
     */
    private static $_instance = null;

    /**
     * Singleton loader.
     *
     * @return self
     */
    public static function get_instance(): self
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
     *
     * @return array
     */
    public function plugin_settings_fields()
    {
        $prefix = "owc-digid-";
        return [
            [
                'title'  => esc_html__('Service Provider (SP)', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Issuer', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}issuer",
                        'required'          => true
                    ],
                    [
                        'label'             => esc_html__('Organization name', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}organization-name",
                    ],
                    [
                        'label'             => esc_html__('Organization url', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}organization-url",
                    ],
                ],
            ],
            [
                'title'  => esc_html__('Identity Provider (IDP)', config('core.text_domain')),
                'fields' => [
                    [
                        'label'             => esc_html__('Metadata url', config('core.text_domain')),
                        'type'              => 'text',
                        'class'             => 'medium',
                        'name'              => "{$prefix}ipd-metadata-url",
                        'required'          => true
                    ]
                ],
            ],
            [
                'title'       => esc_html__('Certificates', config('core.text_domain')),
                'description' => '<p>' . __('Location of the certificates should <strong>not</strong> be publicly accessible and <strong>not</strong> in version control.', config('core.text_domain'))
                    . '<br/>' .
                    __('If site is a multisite, place the certificates in a subdirectory which should be named the <u>ID</u> of the subsite.', config('core.text_domain'))
                    . '<br />' .
                    sprintf(
                        __('E.g., for this site: %s.', config('core.text_domain')),
                        \sprintf('<code>%s/%s</code>', $this->getRootPathToCertificates(), \is_multisite() ? \get_current_blog_id() : '')
                    ) . '</p>',
                'fields'      => [
                    [
                        'label'                => esc_html__('Certificates root location', config('core.text_domain')),
                        'type'                 => 'text',
                        'class'                => 'medium',
                        'name'                 => "{$prefix}location-root-path-certificates",
                        'default_value'        => $this->getRootPathToCertificates(),
                        'required'             => true
                    ],
                    [
                        'label'                => esc_html__('Public certificate location', config('core.text_domain')),
                        'type'                 => 'select',
                        'name'                 => "{$prefix}public-certificate",
                        'choices'              => $this->getPublicCertificates(),
                        'required'             => true
                    ],
                    [
                        'label'                => esc_html__('Private certificate location', config('core.text_domain')),
                        'type'                 => 'select',
                        'name'                 => "{$prefix}private-certificate",
                        'choices'              => $this->getPrivateCertificates(),
                        'required'             => true,
                    ],
                    [
                        'label'					=> esc_html__('Root certificate location', config('core.text_domain')),
                        'type'					=> 'select',
                        'name'					=> "{$prefix}root-certificate",
                        'choices'				=> $this->getRootCertificates(),
                        'required'				=> false
                    ]
                ]
            ],
            [
                // description onder de velden
                'title'       => esc_html__('Session settings', config('core.text_domain')),
                'description' => '<p>' . __('The \'<b>session lifetime</b>\' defines how long the current session is allowed to exist.', config('core.text_domain')) . '</p>',
                'fields' => [
                    [
                        'label'             => esc_html__('Session lifetime', config('core.text_domain')),
                        'type'              => 'select',
                        'class'             => 'medium',
                        'name'              => "{$prefix}lifetime",
                        'choices'           => [
                            [
                                'label' => '5',
                                'value' => '5'
                            ],
                            [
                                'label' => '10',
                                'value' => '10'
                            ],
                            [
                                'label' => '15',
                                'value' => '15'
                            ],
                        ],
                        'required'          => true
                    ],
                    [
                        'label'   => esc_html__('Log out WP User', config('core.text_domain')),
                        'type'    => 'toggle',
                        'name'    => "{$prefix}logout-wp-user",
                        'description' => esc_html__('Automatically log out WP User when session ends.', config('core.text_domain')),
                    ],
                ],
            ]
        ];
    }

    /**
     * Get all the public certificates from the storage map.
     *
     * @return array
     */
    private function getPublicCertificates(): array
    {
        return $this->formatListOfCertificates(glob($this->getCertificateLocation() . '/*.{cer}', GLOB_BRACE));
    }

    /**
     * Format the list of certificates for the selectbox.
     *
     * @param array $certificates
     * @return array
     */
    private function formatListOfCertificates(array $certificates): array
    {
        $noCertificate = [
            [
                'label' => esc_html__('No certificate selected', config('core.text_domain')),
                'value' => 'no-certificate'
            ]
        ];

        $certificates = array_values(array_map(function ($certificate) {
            return [
                'label' => basename($certificate),
                'value' => $certificate,
            ];
        }, $certificates));
        return array_merge($noCertificate, $certificates);
    }

    /**
     * Get all the private certificates from the storage map.
     *
     * @return array
     */
    private function getRootCertificates(): array
    {
        return $this->formatListOfCertificates(glob($this->getCertificateLocation() . '/*.{pem}', GLOB_BRACE));
    }

    private function getPrivateCertificates(): array
    {
        return $this->formatListOfCertificates(glob($this->getCertificateLocation() . '/*.{key}', GLOB_BRACE));
    }

    /**
     * Get the correct path for the certificates of the current site.
     *
     * @return string
     */
    private function getCertificateLocation(): string
    {
        if (is_multisite()) {
            return sprintf('%s/%s', $this->getRootPathToCertificates(), get_current_blog_id() ?? '1');
        }
        return sprintf('%s', $this->getRootPathToCertificates());
    }

    /**
     * Get root path to certificates.
     */
    private function getRootPathToCertificates(): string
    {
        return GravityFormsSettings::make()->get('location-root-path-certificates') ?: storage_path('certificates');
    }
}
