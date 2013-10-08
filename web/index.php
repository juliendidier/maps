<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new Geocoder\Application();
$app['debug'] = $app['config']['debug'];

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/geocode', function (Request $request) use ($app) {
    $address = $request->query->get('address', '');

    if ($address === '') {
        throw new BadRequestHttpException("Address is missing");
    }

    $result = $app['maps.provider']->geocode($address);

    return new Response(json_encode($result, true));
})->bind('geocode');

$app->get('/reverse', function (Request $request) use ($app) {
    $latitude  = $request->query->get('latitude', '');
    $longitude = $request->query->get('longitude', '');

    if ($latitude === '' || $longitude === '') {
        throw new BadRequestHttpException("Latitude (latitude) or longitude (longitude) are missing");
    }

    $result = $app['maps.provider']->reverse($latitude, $longitude);

    return new Response(json_encode($result, true));
})->bind('reverse');

$app->get('/search', function (Request $request) use ($app) {
    $q = $request->query->get('q', '');

    if ($q === '') {
        throw new BadRequestHttpException("Query (q) is missing");
    }

    $result = $app['maps.provider']->search($q);

    return new Response(json_encode($result, true));
})->bind('search');

$app->run();
