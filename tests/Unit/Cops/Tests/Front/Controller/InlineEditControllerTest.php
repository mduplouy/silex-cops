<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InlineEditControllerTest extends AbstractTestCase
{
    public function testUpdateAuthorKoWhenNotLoggedIn()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'John Smith',
            )
        );

        $this->assertFalse($client->getResponse()->isOk());
    }

    public function testUpdateAuthorKoWhenUser()
    {
        $client = $this->createClient();
        //$client->getCookieJar()->clear();

        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'John Smith',
            )
        );

        $this->assertFalse($client->getResponse()->isOk());
    }

    public function testUpdateBook()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        // Update author
        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'John Smith',
            )
        );

        $this->assertTrue($client->getResponse()->isOk());

        // Set back correct author
        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'Victor Hugo',
            )
        );
        $this->assertTrue($client->getResponse()->isOk());

        // Test with french locale
        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'pubdate',
                'pk'    => 3,
                'value' => '01/12/1970', // d/m/Y
            )
        );
        $this->assertTrue($client->getResponse()->isOk());

        // Test with english locale
        $client->request(
            'POST',
            '/default/en/inline-edit/3',
            array(
                'name'  => 'pubdate',
                'pk'    => 3,
                'value' => '12/01/1970', // m/d/Y
            )
        );
        $this->assertTrue($client->getResponse()->isOk());

        // Update title
        $client->request(
            'POST',
            '/default/en/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'Les miséroïdes',
            )
        );
        $this->assertTrue($client->getResponse()->isOk());

        $client->request(
            'POST',
            '/default/en/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'Les misérables',
            )
        );
       $this->assertTrue($client->getResponse()->isOk());
    }

    public function testEditActionWrongNameException()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'dummy-field',
                'pk'    => 3,
                'value' => 'dummy-value',
            )
        );

        $this->assertFalse($client->getResponse()->isOk());
    }


    public function testEditActionWrongBookReturnsEmptyResponse()
    {

        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request(
            'POST',
            '/test/fr/inline-edit/123456',
            array()
        );

        $this->assertEquals($client->getResponse()->getContent(), '');
    }

    public function testUpdateBookComment()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $book = $this->app['entity.book']->findById(3);

        $origComment = $book->getComment();

        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'comment',
                'pk'    => 3,
                'value' => 'New comment for this book',
            )
        );

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testUpdateBookTags()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $book = $this->app['entity.book']->findById(3);

        $origTags = array();
        foreach($book->getTags()->findByBookId($book->getId()) as $tag) {
            $origTags[] = $tag->getName();
        }

        $newTags = $origTags;
        $newTags[] = 'new tag';

        // Add new tag
        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'tags',
                'pk'    => 3,
                'value' => $newTags,
            )
        );

        // Restore original tags
        $client->request(
            'POST',
            '/test/fr/inline-edit/3',
            array(
                'name'  => 'tags',
                'pk'    => 3,
                'value' => $origTags,
            )
        );

        $this->assertTrue($client->getResponse()->isOk());
    }
}
