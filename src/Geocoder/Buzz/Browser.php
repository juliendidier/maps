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
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?language=fr&address='.$address.'&sensor=false';
        $content = $this->getJson($url);

        return $this->processContent($content);
    }

    public function getReverse($latitude, $longitude)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?language=fr&latlng='.$latitude.','.$longitude.'&sensor=false';
        $content = $this->getJson($url);

        return $this->processContent($content);
    }

    public function getSearch($q)
    {
        $q = urlencode($q);
        // $url = 'https://maps.googleapis.com/maps/api/autocomplete/json?input='.$q.'&types=geocode&language=fr&sensor=false&key='.$this->apiKey;
        $url = "https://maps.googleapis.com/maps/api/geocode/json?language=fr&address=".$q."&sensor=false";
        $content = $this->getJson($url);

        $autocomplete = [];
        foreach ($content['results'] as $address) {
            $data = [
                'address' => $address['formatted_address'],
                'location' => [
                    'latitude' => $address['geometry']['location']['lat'],
                    'longitude' => $address['geometry']['location']['lng'],
                ],
            ];

            $autocomplete[] = $data;
        }

        return $autocomplete;
    }

    protected function getJson($url, $headers = array())
    {
        $response = $this->get($url);

        if (!$response->isSuccessful()) {
            throw new \Exception("Failed to communicate with google maps apis");
        }

        $content = json_decode($response->getContent(), true);
        if (array_key_exists('status', $content) && $content['status'] === 'REQUEST_DENIED') {
            throw new \Exception(sprintf('Google maps apis says %s', $content['status']));
        }

        return json_decode($response->getContent(), true);
    }

    protected function processContent(array $content)
    {
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
}
