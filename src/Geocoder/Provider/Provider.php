<?php

namespace Geocoder\Provider;

use Geocoder\Buzz\Browser;
use Geocoder\Cache\Cache;

class Provider
{
    protected $browser;
    protected $cache;

    public function __construct(Browser $browser, Cache $cache)
    {
        $this->browser  = $browser;
        $this->cache    = $cache;
    }

    public function reverse($latitude, $longitude)
    {
        if ($this->cache->hasReverse($latitude, $longitude)) {
            return $this->cache->getReverse($latitude, $longitude);
        }

        $reverse = $this->browser->getReverse($latitude, $longitude);
        $this->cache->setReverse($latitude, $longitude, $reverse);

        return $reverse;
    }
}
