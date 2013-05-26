<?php

namespace Geocoder\Cache;

use Predis\Client;

class Cache
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function hasGeocode($address)
    {
        return $this->client->exists($this->getGeocodeKey($address));
    }

    public function getGeocode($address)
    {
        if (!$this->hasGeocode($address)) {
            throw new \LogicException(sprintf('No cache for reverse "%f,%f', $address));
        }

        return json_decode($this->client->get($this->getGeocodeKey($address)), true);
    }

    public function setGeocode($address, array $data)
    {
        $this->client->set($this->getGeocodeKey($address), json_encode($data, true));
    }

    public function hasReverse($latitude, $longitude)
    {
        return $this->client->exists($this->getReverseKey($latitude, $longitude));
    }

    public function getReverse($latitude, $longitude)
    {
        if (!$this->hasReverse($latitude, $longitude)) {
            throw new \LogicException(sprintf('No cache for reverse "%f,%f', $latitude, $longitude));
        }

        return json_decode($this->client->get($this->getReverseKey($latitude, $longitude)), true);
    }

    public function setReverse($latitude, $longitude, array $data)
    {
        $this->client->set($this->getReverseKey($latitude, $longitude), json_encode($data, true));
    }

    protected function getReverseKey($latitude, $longitude)
    {
        return 'reverse_'.md5('lat'.$latitude.'lng'.$longitude);
    }

    protected function getGeocodeKey($address)
    {
        return 'geocode_'.md5('addr'.$address);
    }
}
