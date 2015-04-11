<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core;

/**
 * String utilities
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class StringUtils
{
    /**
     * Get alphabetic letters
     *
     * @return array
     */
    public function getLetters()
    {
        return array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        );
    }

    /**
     * Remove accent from a string
     *
     * @param string $str
     * @param string $charset
     *
     * @return string
     */
    public function removeAccents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        return $str;
    }

    /**
     * Make a string url safe
     *
     * @param string $string
     *
     * @return string
     */
    public function urlSafe($string)
    {
        $string = $this->removeAccents($string);
        $string = preg_replace('/[^\w]/', '-', $string);
        return preg_replace('/-{2,}/', '-', $string);
    }
}
