<?php
require 'vendor/autoload.php';

use Mikron\CampaignOrganiser\Page;

$app = new \Slim\Slim();

$app->get('/', function () {
    $page = new Page("index");
    echo $page->getContent();
});

$app->get('/:page', function ($uri) {
    $page = new Page($uri);
    echo $page->getContent();
});
$app->run();