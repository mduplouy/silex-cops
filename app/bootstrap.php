<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->get('/hello', function() {
    return 'Hello!';
});

$app->get('/', 'Cops\Controller\IndexController::indexAction');

return $app;