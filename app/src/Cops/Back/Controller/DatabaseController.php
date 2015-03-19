<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Back\Controller;

use Silex\ControllerProviderInterface;
use Cops\Core\Application;

/**
 * Admin related controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class DatabaseController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/triggers', __CLASS__.'::triggerAction')
            ->bind('admin_database_triggers');

        $controller->post('/triggers', __CLASS__.'::saveTriggerAction')
            ->bind('admin_database_triggers_action');

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
        $triggers = $app['calibre-util']->getTriggers();
        $checkTriggers = $app['calibre-util']->loadExistingTriggers();

        $foundTriggers = array();
        foreach ($checkTriggers as $trigger) {
            $foundTriggers[$trigger['name']] = true;
        }

        return $app['twig']->render('admin/database/trigger.html.twig', array(
            'triggers'      => $triggers,
            'foundTriggers' => $foundTriggers,
            'dbSelectRoute' => 'admin_database_triggers',
        ));
    }

    /**
     * Save trigger action, remove or restore selected triggers
     *
     * @param  Application $app Application instance
     *
     * @return RedirectResponse
     */
     public function saveTriggerAction(Application $app)
     {
        $repository = $app['calibre-util']->getRepository();

        foreach ($app['request']->get('triggers') as $trigger => $value) {
            if (empty($value)) {
                $repository->dropTrigger($trigger);
            } else {
                $repository->restoreTrigger($trigger);
            }
        }

        return $app->redirect($app['url_generator']->generate('admin_database_triggers'));
     }
}
