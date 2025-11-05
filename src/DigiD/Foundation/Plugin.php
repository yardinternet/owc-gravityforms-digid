<?php

declare(strict_types=1);

namespace Yard\DigiD\Foundation;

use function DI\create;
use Exception;
use function Yard\DigiD\Foundation\Helpers\resolve;

class Plugin
{
    /**
     * Name of the plugin.
     *
     * @var string
     */
    public const NAME = 'owc-gravityforms-digid';

    /**
     * Version of the plugin.
     *
     * @var string
     */
    public const VERSION = GF_DIGID_VERSION;

    protected string $rootPath;
    protected Config $config;
    protected \DI\Container $container;

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
     */
    private function __construct(string $rootPath = '')
    {
        $this->rootPath = $rootPath;
        require_once __DIR__ .'/Helpers.php';
        $this->buildContainer();
    }

    /**
     * Return the Plugin instance
     */
    public static function getInstance(string $rootPath = ''): self
    {
        if (null === static::$instance) {
            static::$instance = new static($rootPath);
        }

        return static::$instance;
    }

    protected function buildContainer(): void
    {
        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions([
            'app' => $this,
            'config' => create(Config::class)->constructor($this->rootPath . '/config'),
            'route' => function () {
                return	new \Yard\DigiD\Foundation\Routing\Router(\is_multisite() ? \get_blog_details()->path : '');
            },
            'session' => function () {
                $sharedAuraInstance = apply_filters('yard_aura_session_instance', null);

                if ($sharedAuraInstance instanceof \Aura\Session\Session) {
                    return $sharedAuraInstance;
                }

                $session_factory = new \Aura\Session\SessionFactory;
                $session = $session_factory->newInstance($_COOKIE);
                $session->setCookieParams([
                    'secure' => true,
                    'httponly' => true,
                ]);

                return $session;
            },
            'logger' => function () {
                $logger = new \Monolog\Logger('gfdigid_log');
                $maxFiles = apply_filters('owc_gravityforms_digid_rotating_filer_handler_max_files', GF_DIGID_LOGGER_DEFAULT_MAX_FILES);

                $handler = (new \Monolog\Handler\RotatingFileHandler(
                    filename:  sprintf('%s/gfdigid-log.json', dirname(ABSPATH)),
                    maxFiles: is_int($maxFiles) && 0 < $maxFiles ? $maxFiles : GF_DIGID_LOGGER_DEFAULT_MAX_FILES,
                    level: \Monolog\Level::Debug
                ))->setFormatter(new \Monolog\Formatter\JsonFormatter());

                $logger->pushHandler($handler);
                $logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

                return $logger;
            },
        ]);

        $this->container = $builder->build();
    }

    /**
     * Return container
     */
    public function getContainer(): \DI\Container
    {
        return $this->container;
    }

    /**
     * Boot the plugin.
     */
    public function boot(): void
    {
        $this->config = resolve('config');

        $this->loadTextDomain();
        $this->bootServiceProviders();
    }

    private function loadTextDomain(): void
    {
        load_plugin_textdomain($this->getName(), false, $this->getName() . '/languages/');
    }

    /**
     * Get the name of the plugin.
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Get the version of the plugin.
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get the root path of the plugin.
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Get the path to a particular resource.
     */
    public function resourceUrl(string $file, string $directory = ''): string
    {
        $directory = ! empty($directory) ? $directory .'/' : '';

        return plugins_url("resources/{$directory}/{$file}", GF_DIGID_PLUGIN_SLUG .'/plugin.php');
    }

    /**
     * Boot service providers.
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

            if (! $service instanceof ServiceProvider) {
                throw new Exception('Provider must extend ServiceProvider.', 500);
            }

            $service->register();
        }
    }

    /**
     * Get instance of the configuration repository.
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
