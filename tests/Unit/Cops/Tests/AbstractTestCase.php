<?php

namespace Cops\Tests;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractTestCase extends WebTestCase
{
    public function createApplication()
    {
        $params = array(
            'config-file' => __DIR__.'/../../../../app/src/config.ini',
        );

        $override = array(
            'data_dir'    => array(
                'test'    => __DIR__.'/../../../data/',
                'default' => __DIR__.'/../../../data/',
            ),
            'internal_db' => __DIR__.'/../../../data/silexCops',
        );

        $app = new \Cops\Core\Application($params, $override);

        $app['debug'] = true;
        $app['session.test'] = true;
        return $app;
    }

    public function createClient(array $server = array())
    {
        $client = parent::createClient($server);

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
