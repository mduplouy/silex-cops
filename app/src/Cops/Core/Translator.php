<?php

/*
* This file is part of the Silex framework.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * Overriden to fix the bug about locale
 * see https://github.com/silexphp/Silex/commit/902c2c8c6bf70f8703a55de42b06378ec94c913d
 */
namespace Cops\Core;

use Symfony\Component\Translation\Translator as BaseTranslator;
use Silex\Application as BaseApplication;
use Symfony\Component\Translation\MessageSelector;

/**
* Translator that gets the current locale from the Silex application.
*
* @author Fabien Potencier <fabien@symfony.com>
*/
class Translator extends BaseTranslator
{
    protected $app;

    public function __construct(BaseApplication $app, MessageSelector $selector)
    {
        $this->app = $app;

        parent::__construct(null, $selector);
    }

    public function getLocale()
    {
        return $this->app['locale'];
    }
}
