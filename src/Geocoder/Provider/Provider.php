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

    public function geocode($address)
    {
        if ($this->cache->hasGeocode($address)) {
            return $this->cache->getGeocode($address);
        }

        $geocode = $this->browser->getGeocode($address);
        $this->cache->setGeocode($address, $geocode);

        return $geocode;
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
