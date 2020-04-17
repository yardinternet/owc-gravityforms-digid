<?php

namespace Yard\DigiD\Foundation;

use function Yard\DigiD\Foundation\Helpers\resolve;

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
    const VERSION = GF_DIGID_VERSION;

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
    private function __construct($rootPath = '')
    {
        $this->rootPath = $rootPath;
        require_once __DIR__ .'/Helpers.php';
        $this->buildContainer();
        load_plugin_textdomain($this->getName(), false, $this->getName() . '/languages/');
    }

    /**
     * Return the Plugin instance
     *
     * @param string $rootPath
     *
     * @return self
     */
    public static function getInstance($rootPath = '') : self
    {
        if (null == static::$instance) {
            static::$instance = new static($rootPath);
        }

        return static::$instance;
    }

    /**
     * @return \DI\Container
     */
    protected function buildContainer()
    {
        $builder         = new \DI\ContainerBuilder();
        $builder->addDefinitions([
            'app'	     => $this,
            'config'   => function () {
                return new \Yard\DigiD\Foundation\Config($this->rootPath.'/config');
            },
            'loader'   => Loader::getInstance(),
            'route'    => function () {
                return	new \Yard\DigiD\Foundation\Routing\Router(get_blog_details()->path ?? '');
            },
            'session'  => function () {
                $session_factory         = new \Aura\Session\SessionFactory;
                $session         = $session_factory->newInstance($_COOKIE);
                $session->start();
                return $session;
            },
            'teams'    => function () {
                return (new \Monolog\Logger('microsoft-teams-logger'))->pushHandler(new \Rspeekenbrink\MonologMicrosoftTeams\MicrosoftTeamsHandler(
                    getenv('MS_TEAMS_WEBHOOK'),
                    $this->getName(),
                    \Monolog\Logger::INFO
                ));
            }
        ]);
        $this->container = $builder->build();
    }

    /**
     * Return container
     *
     * @return \DI\Container
     */
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
        $this->config = resolve('config');
        $this->loader = resolve('loader');

        $this->bootServiceProviders();

        $this->loader->register();
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
                throw new \Exception('Provider must extend ServiceProvider.');
            }

            $service->register();
        }
    }
}
