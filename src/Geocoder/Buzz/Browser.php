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

    public function getGeocode($address)
    {
        $address  = urlencode($address);
        $url      = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false&key='.$this->apiKey;
        $response = $this->get($url);

        if (!$response->isSuccessful()) {
            throw new \Exception(sprintf('Failed to communicate with google maps apis (%d)', $response->getStatusCode()));
        }

        $content  = json_decode($response->getContent(), true);

        if (array_key_exists('status', $content) && $content['status'] === 'REQUEST_DENIED') {
            throw new \Exception("Google maps apis says REQUEST_DENIED");
        }

        $result = array(
            'location'  => $content['results'][0]['geometry']['location'],
            'accuracy'  => $content['results'][0]['geometry']['location_type'],
            'addresses' => $content['results'][0]['address_components'],
            'address'   => array(),
        );

        if (count($content['results'][0]['address_components']) > 0) {
            foreach ($content['results'][0]['address_components'] as $addressNode) {
                foreach ($addressNode['types'] as $nodeType) {
                    if ($nodeType === 'political') {
                        continue;
                    }

                    $result['address'][$nodeType] = $addressNode['long_name'];
                }
            }
        }

        return $result;
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
