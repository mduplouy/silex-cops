<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Silex\Application as BaseApplication;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Psr\Log\LoggerInterface;

/**
 * Overrided the class to be able to deactive url rewriting
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    private $app;

    /**
     * @inheritDoc
     */
    public function __construct(RouteCollection $routes, RequestContext $context, LoggerInterface $logger = null, BaseApplication $app)
    {
        parent::__construct($routes, $context, $logger);
        $this->app = $app;
    }

    /**
     * @inheritDoc
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        // @codeCoverageIgnoreStart
        // Always inject database parameter if empty
        $defaults['database'] = $this->app['config']->getValue('current_database_key');

        $url = parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);

        // Check for mod_rewrite config then prepend script name to url
        if (!$this->app['config']->getValue('use_rewrite') && PHP_SAPI != 'cli') {
            $url = $this->addScriptNameToUrl($url);
        }

        // @codeCoverageIgnoreEnd
        return $url;
    }

    /**
     * Add script name to url when not using rewrite
     *
     * @param  string $url
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    private function addScriptNameToUrl($url)
    {
        $scriptName = $this->app['request']->getScriptName();

        if (strpos($url, $scriptName) === false) {
            $url = $this->app['request']->getBasePath().DS.basename($scriptName).$url;
        }
        return $url;
    }
}
