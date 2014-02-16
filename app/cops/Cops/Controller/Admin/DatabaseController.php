<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Controller\Admin;

use Cops\Model\Controller;
use Silex\ControllerProviderInterface;
use Silex\Application;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * Admin related controller
 */
class DatabaseController extends Controller implements ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see    ControllerProviderInterface::connect()
     *
     * @param  Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/triggers', __CLASS__.'::triggerAction')
            ->bind('admin_database_triggers');

        return $controller;
    }

    /**
     * Trigger action, remove / restore some calibre database triggers
     *
     * @param  Application $app Application instance
     *
     * @return string
     */
    public function triggerAction(Application $app)
    {

        $triggers = $app['calibre']->getTriggers();

        $checkTriggers = $app['db']->createQueryBuilder()
            ->select('name')
            ->from('SQLite_Master', 'main')
            ->where('name IN(:trigger_name)')
            ->setParameter('trigger_name', array_keys($triggers), Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

        $foundTriggers = array();
        foreach($checkTriggers as $trigger) {
            $foundTriggers[$trigger['name']] = true;
        }

        return $app['twig']->render('admin/database/trigger.html', array(
            'triggers'      => $triggers,
            'foundTriggers' => $foundTriggers,
        ));
    }
}