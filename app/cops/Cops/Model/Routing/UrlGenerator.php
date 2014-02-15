<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Routing;

use Cops\Model\Core;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Overrided the class to be able to deactive url rewriting
 */
class UrlGenerator extends \Symfony\Component\Routing\Generator\UrlGenerator
{
    /**
     * @inheritDoc
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        $url = parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);

        // Check for mod_rewrite config then prepend script name to url
        if (Core::getConfig()->getValue('use_rewrite') !== true && php_sapi_name() != 'cli') {
            $url = $this->addScriptNameToUrl($url);
        }
        return $url;
    }

    /**
     * Add script name to url when not using rewrite
     *
     * @param  string $url
     *
     * @return string
     */
    private function addScriptNameToUrl($url)
    {
        $app = Core::getApp();
        $scriptName = $app['request']->getScriptName();

        if (strpos($url, $scriptName) === false) {
            $url = $app['request']->getBasePath().DS.basename($scriptName).$url;
        }
        return $url;
    }
}