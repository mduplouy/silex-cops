<?php
// @codeCoverageIgnoreStart
$app = new \Cops\Model\Application();

// Load & set configuration
$app['config'] = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini');

// Define core model
$app['core'] =  new \Cops\Model\Core($app);

$app['debug'] = true;
$app['session.test'] = true;

// Register special database for tests
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array(
        'calibre' => array(
            'driver'        => 'pdo_sqlite',
            'path'          => DATABASE,
            'driverOptions' => \Cops\Model\Calibre::getDBInternalFunctions(),
        ),
        'silexCops' => array(
            'driver' => 'pdo_sqlite',
            'path'   => BASE_DIR . $app['config']->getValue('internal_db'),
        ),
    ),
));

$users =  array(
    // test : test
    'test' => array('ROLE_USER', 'stHGdg4MhYOm/OVTWjpMJievIvJqafsQQ3WpWlUNDT6WfHupVWjBQaxdppMQkdCmYSXl6QQQXVYLGL/MDZi5Zw=='),
    // admin : test
    'admin' => array('ROLE_ADMIN', 'stHGdg4MhYOm/OVTWjpMJievIvJqafsQQ3WpWlUNDT6WfHupVWjBQaxdppMQkdCmYSXl6QQQXVYLGL/MDZi5Zw==')
);

// Register security provider with less security
$app->register(new \Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'http' => true,
            'users' => $users
        ),
        'default' => array(
            'pattern' => '^.*$',
            'http' => true,
            'anonymous' => true,
            'users' => $users
        ),
    )
));

$app['security.role_hierarchy'] = array(
    'ROLE_ADMIN' => array('ROLE_USER','ROLE_EDIT'),
    'ROLE_EDIT'  => array('ROLE_USER'),
);

$app['security.access_rules'] = array(
     array('^/../admin',        'ROLE_ADMIN'),
     array('^/../inline-edit/', 'ROLE_EDIT')
);

$app['book_storage_dir'] = BASE_DIR.'tests/data';

return $app;
// @codeCoverageIgnoreEnd