<?php

namespace Geocoder\Provider;

use Geocoder\Buzz\Browser;
use Geocoder\Cache\Exception\NotCachedException;
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
        try {
            $geocode = $this->cache->getGeocode($address);
        } catch (NotCachedException $e) {
            $geocode = $this->browser->getGeocode($address);
            $this->cache->setGeocode($address, $geocode);
        }

        return $geocode;
    }

    public function reverse($latitude, $longitude)
    {
        try {
            $reverse = $this->cache->getReverse($latitude, $longitude);
        } catch (NotCachedException $e) {
            $reverse = $this->browser->getReverse($latitude, $longitude);
            $this->cache->setReverse($latitude, $longitude, $reverse);
        }

        return $reverse;
    }

    public function search($q)
    {
        $search = [];
        try {
            $search['results'] = $this->cache->getSearch($q);
        } catch (NotCachedException $e) {
            $search['results'] = $this->browser->getSearch($q);
            $this->cache->setSearch($q, $search);
        }

        return $search;
    }
}
