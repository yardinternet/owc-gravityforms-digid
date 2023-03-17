<?php

namespace Tests\Yard\DigiD\Foundation;

use Tests\Yard\DigiD\TestCase;
use WP_Mock;
use Yard\DigiD\Foundation\Plugin;

class PluginTest extends TestCase
{
    /** @var Plugin */
    protected $plugin;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $getBlogDetails = new \StdClass;
        $getBlogDetails->path = '';
        WP_Mock::userFunction('get_blog_details', [
            'return' => $getBlogDetails
        ]);

        WP_Mock::passthruFunction('load_plugin_textdomain');
        $this->plugin = Plugin::getInstance('test');
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function plugin_object_is_instance_of_plugin_class(): void
    {
        $this->assertInstanceOf(Plugin::class, $this->plugin);
    }

    /** @test */
    public function name_of_plugin_is_correct()
    {
        $this->assertEquals('owc-gravityforms-digid', $this->plugin->getName());
    }

    /** @test */
    public function version_of_plugin_is_correct()
    {
        $this->assertEquals(GF_DIGID_VERSION, $this->plugin->getVersion());
    }

    /** @test */
    public function rootpath_of_plugin_is_correct()
    {
        $this->assertEquals('test', $this->plugin->getRootPath());
    }

    /** @test */
    public function container_is_returned_correctly()
    {
        $this->assertInstanceOf(\DI\Container::class, $this->plugin->getContainer());
    }

    /** @test */
    public function resource_url_returns_correct_path()
    {
        WP_Mock::userFunction('plugins_url')
            ->andReturn('/app/htdocs/wp-content/plugins/owc-gravityforms-digid/resources/js/test.js');
        $testJS = $this->plugin->resourceUrl('test.js', 'js');
        $this->assertEquals($testJS, '/app/htdocs/wp-content/plugins/owc-gravityforms-digid/resources/js/test.js');
    }
}
