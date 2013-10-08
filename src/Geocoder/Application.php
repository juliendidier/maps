<?php

namespace Geocoder;

use Predis\Client;
use Geocoder\Cache\Cache;
use Geocoder\Provider\Provider;
use Silex\Application as SilexApplication;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Buzz\Client\Curl;

class Application extends SilexApplication
{
    public function __construct()
    {
        parent::__construct();

        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('config.yml')
            ->in(__DIR__.'/../../app')
        ;

        $this['config'] = array();
        foreach ($iterator as $file) {
            $yaml   = new Yaml();
            $config = $yaml->parse($file->getRealPath());
            $this['config'] = array_merge($this['config'], $config);
        }

        $this['cache.client'] = function ($app) {
            return new Client(array(
                'host'     => $this['config']['cache']['client']['host'],
                'port'     => $this['config']['cache']['client']['port'],
                'database' => $this['config']['cache']['client']['database'],
            ));
        };

        $this['maps.cache'] = function ($app) {
            return new Cache($app['cache.client']);
        };

        $this['buzz.browser'] = function ($app) {
            $browser = new Buzz\Browser(new Curl());
            $browser->setApiKey($app['config']['maps']['apiKey']);

            return $browser;
        };

        $this['maps.provider'] = function ($app) {
            return new Provider($app['buzz.browser'], $app['maps.cache']);
        };
    }
}
