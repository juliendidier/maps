<?php

namespace Geocoder\Buzz;

use Buzz\Browser as BaseBrowser;

class Browser extends BaseBrowser
{
    protected $apiKey = null;

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getReverse($latitude, $longitude)
    {
        $url      = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false&key='.$this->apiKey;
        $response = $this->get($url);

        if (!$response->isSuccessful()) {
            throw new \Exception("Failed to communicate with google maps apis");
        }

        $content  = json_decode($response->getContent(), true);

        if (array_key_exists('status', $content) && $content['status'] === 'REQUEST_DENIED') {
            throw new \Exception("Google maps apis says REQUEST_DENIED");
        }

        return $content['results'];
    }
}
