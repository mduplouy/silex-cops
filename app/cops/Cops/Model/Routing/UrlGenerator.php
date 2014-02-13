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
            $app = Core::getApp();
            $scriptName = $app['request']->getScriptName();

            if (strpos($url, $scriptName) === false) {
                $basePath = $app['request']->getBasePath();
                if ($basePath == '') {
                    $url = $basePath.basename($scriptName).$url;
                } else {
                    $url = str_replace(
                        $basePath,
                        $basePath.DS.basename($scriptName),
                        $url
                    );
                }
            }
        }
        return $url;
    }
}