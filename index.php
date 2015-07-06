<?php

require 'vendor/autoload.php';

use Mikron\CampaignOrganiser\Page;

$app = new \Slim\Slim();

$app->notFound(function () use ($app) {
    $page = new Page("404", $app);
    echo $page->getContent();
});

$app->get('/', function () use ($app) {
    $page = new Page("index", $app);
    echo $page->getContent();
});

$app->get('/:page', function ($uri) use ($app) {
    $page = new Page($uri, $app);
    echo $page->getContent();
});

$app->run();
