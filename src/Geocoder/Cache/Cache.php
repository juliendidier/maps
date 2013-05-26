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
}
