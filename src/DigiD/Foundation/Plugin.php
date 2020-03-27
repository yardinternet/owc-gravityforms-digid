<?php

namespace Yard\DigiD\Foundation;

use DI\ContainerBuilder;
use Exception;

class Plugin
{
    /**
     * Name of the plugin.
     *
     * @var string
     */
    const NAME = 'owc-gravityforms-digid';

    /**
     * Version of the plugin.
     *
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * Path to the root of the plugin.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Instance of the configuration repository.
     *
     * @var \Yard\DigiD\Foundation\Config
     */
    public $config;

    /**
     * Instance of the hook loader.
     *
     * @var \Yard\DigiD\Foundation\Loader
     */
    public $loader;

    /**
     * @var \DI\Container
     */
    protected $container;

    /**
     * @var Plugin
     */
    protected static $instance;

    /**
     * Constructs the plugin.
     *
     * Create startup hooks and tear down hooks.
     * Boot up admin and frontend functionality.
     * Register the actions and filters from the loader.
     *
     * @param string $rootPath
     */
    public function __construct($rootPath = '')
    {
        $this->buildContainer();
        $this->rootPath = $rootPath;
        load_plugin_textdomain($this->getName(), false, $this->getName() . '/languages/');
    }

    public static function getInstance() : self
    {
        if (null == static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return \DI\Container
     */
    protected function buildContainer()
    {
        $builder         = new ContainerBuilder();
        $builder->addDefinitions([
            'session' => new \duncan3dc\Sessions\SessionInstance($this->getName()),
            'route'   => new \Yard\DigiD\Foundation\Routing\Router(get_blog_details()->path ?? '')
        ]);
        $this->container = $builder->build();
    }

    public function getContainer() :  \DI\Container
    {
        return $this->container;
    }

    /**
     * Boot the plugin.
     *
     * @return void
     */
    public function boot(): void
    {
        require_once __DIR__ .'/Helpers.php';

        $this->config = new Config($this->rootPath.'/config');
        $this->config->boot();

        $this->loader = Loader::getInstance();

        $this->bootServiceProviders();

        $this->loader->addAction('wp_enqueue_scripts', $this, 'enqueueScripts');
        $this->loader->register();
    }

    /**
     * Enqueue scripts within WordPress.
     *
     * @return void
     */
    public function enqueueScripts(): void
    {
        wp_enqueue_style(GF_DIGID_PLUGIN_SLUG, $this->resourceUrl(GF_DIGID_PLUGIN_SLUG .'.css', 'css'), []);
    }

    /**
     * Get the name of the plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Get the version of the plugin.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get the root path of the plugin.
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Get the path to a particular resource.
     *
     * @var string $file
     * @var string $directory
     *
     * @return string
     */
    public function resourceUrl(string $file, string $directory = ''): string
    {
        $directory = !empty($directory) ? $directory .'/' : '';
        return plugins_url("resources/{$directory}/{$file}", GF_DIGID_PLUGIN_SLUG .'/plugin.php');
    }

    /**
     * Boot service providers.
     *
     * @return void
     */
    protected function bootServiceProviders(): void
    {
        $services = $this->config->get('core.providers');

        foreach ($services as $service) {
            // Only boot global service providers here.
            if (is_array($service)) {
                continue;
            }

            $service = new $service($this);

            if (!$service instanceof ServiceProvider) {
                throw new Exception('Provider must extend ServiceProvider.');
            }

            $service->register();
        }
    }
}
