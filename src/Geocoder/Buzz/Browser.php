<?php

namespace Geocoder\Buzz;

use Buzz\Browser as BuzzBrowser;

class Browser extends BuzzBrowser
{
    protected $apiKey = null;

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getGeocode($address)
    {
        $address = urlencode($address);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false';
        $content = $this->getJson($url);

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
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false';
        $content = $this->getJson($url);

        return $content['results'];
    }

    public function getSearch($q)
    {
        $q = urlencode($q);
        // $url = 'https://maps.googleapis.com/maps/api/autocomplete/json?input='.$q.'&types=geocode&language=fr&sensor=false&key='.$this->apiKey;
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?input=Vict&sensor=true&key=".$this->apiKey;
        $content = $this->getJson($url);

die(var_dump($content));
        return $content['results'];
    }

    public function getJson($url, $headers = array())
    {
        $response = $this->get($url);
die();
        if (!$response->isSuccessful()) {
            throw new \Exception("Failed to communicate with google maps apis");
        }

        if (array_key_exists('status', $content) && $content['status'] === 'REQUEST_DENIED') {
            throw new \Exception(sprintf('Google maps apis says %s', $content['status']));
        }

        return json_decode($response->getContent(), true);
    }
}
