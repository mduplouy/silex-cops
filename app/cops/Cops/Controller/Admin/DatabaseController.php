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

use Silex\ControllerProviderInterface;
use Silex\Application;

/**
 * Admin related controller
 */
class DatabaseController implements ControllerProviderInterface
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
        $triggers = $app['model.calibre']->getTriggers();
        $checkTriggers = $app['model.calibre']->loadExistingTriggers();

        $foundTriggers = array();
        foreach ($checkTriggers as $trigger) {
            $foundTriggers[$trigger['name']] = true;
        }

        return $app['twig']->render('admin/database/trigger.html', array(
            'triggers'      => $triggers,
            'foundTriggers' => $foundTriggers,
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
        foreach($app['request']->get('triggers') as $trigger => $value) {
            if (empty($value)) {
                $app['model.calibre']->getResource()->dropTrigger($trigger);
            } else {
                $app['model.calibre']->getResource()->restoreTrigger($trigger);
            }
        }
        return $app->redirect($app['url_generator']->generate('admin_database_triggers'));
     }
}