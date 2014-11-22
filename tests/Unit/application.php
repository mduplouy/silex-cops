<?php
// @codeCoverageIgnoreStart
$app = new \Silex\Application();

// Load & set configuration
$app['config'] = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini', new \Cops\Model\Utils);

$app['config']->setValue('data_dir', array(
    'default' => 'tests/data',
    'test'    => 'tests/data',
));

$app['config']->setValue('internal_db', 'tests/data/silexCops');
$app['config']->setValue('current_database_key',  'unit-test');

// Define core model
$app['core'] =  new \Cops\Model\Core($app);

$app['debug'] = true;
$app['session.test'] = true;

return $app;
// @codeCoverageIgnoreEnd