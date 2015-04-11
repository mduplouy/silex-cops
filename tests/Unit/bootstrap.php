<?php

$loader = require __DIR__.'/../../vendor/autoload.php';

$loader->addPsr4('Cops\\Tests\\', __DIR__.'/Cops/Tests', true);

$app = require __DIR__.'/../../app/bootstrap.php';

return $app;
