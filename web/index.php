<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new Geocoder\Application();
$app['debug'] = $app['config']['debug'];

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/reverse', function (Request $request) use ($app) {
    $latitude  = $request->query->get('latitude', '');
    $longitude = $request->query->get('longitude', '');

    if ($latitude === '' || $longitude === '') {
        throw new BadRequestHttpException("Latitude or longitude are missing");
    }

    $result = $app['maps.provider']->reverse($latitude, $longitude);

    return new Response(json_encode($result, true));
})->bind('reverse');

$app->run();
