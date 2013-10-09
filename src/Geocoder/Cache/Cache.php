<?php

namespace Geocoder\Cache;

use Geocoder\Cache\Exception\NotCachedException;
use Predis\Client;

class Cache
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getGeocode($address)
    {
        $key = $this->getGeocodeKey($address);

        if (!$this->client->exists($key)) {
            throw new NotCachedException(sprintf('No cache for reverse "%s', $address));
        }

        return unserialize($this->client->get($key));
    }

    public function setGeocode($address, $data)
    {
        $this->client->set($this->getGeocodeKey($address), serialize($data));
    }

    public function getReverse($latitude, $longitude)
    {
        $key = $this->getReverseKey($latitude, $longitude);

        if (!$this->client->exists($key)) {
            throw new NotCachedException(sprintf('No cache for reverse "%f,%f', $latitude, $longitude));
        }

        return unserialize($this->client->get($key));
    }

    public function setReverse($latitude, $longitude, $data)
    {
        $this->client->set($this->getReverseKey($latitude, $longitude), serialize($data));
    }

    public function getSearch($q)
    {
        $key = $this->getSearchKey($q);

        if (!$this->client->exists($key)) {
            throw new NotCachedException(sprintf('No cache for search "%s', $q));
        }

        return unserialize($this->client->get($key));
    }

    public function setSearch($q, $data)
    {
        $this->client->set($this->getSearchKey($q), serialize($data));
    }

    protected function getReverseKey($latitude, $longitude)
    {
        return 'reverse_'.md5(sprintf('lat%flng%f', $latitude, $longitude));
    }

    protected function getGeocodeKey($address)
    {
        return 'geocode_'.md5(sprintf('addr%s', $address));
    }

    protected function getSearchKey($q)
    {
        return 'search_'.md5(sprintf('q%s', $q));
    }
}
