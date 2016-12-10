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
    const TRANSLITERATE_RULE = 'Any-Latin; Latin-ASCII; [\u0100-\u7fff] remove';

    /**
     * Use transliterate ?
     * @var bool
     */
    private $useTransliterate = false;

    /**
     * Transliterate strings to this encoding
     * @var string
     */
    private $transliterateTo;

    /**
     * Transliterator instance
     * @var \Transliterator
     */
    private $transliterator;

    /**
     * Inverse transliterator instance
     * @var type
     */
    private $inverseTransliterator;

    /**
     * Set use transliterate
     *
     * @param bool $useTransliterate
     *
     * @return $this
     */
    public function setUseTransliterate($useTransliterate)
    {
        $this->useTransliterate = (bool) $useTransliterate;

        return $this;
    }

    /**
     * Use transliterate
     *
     * @return bool
     */
    public function useTransliterate()
    {
        return $this->useTransliterate === true;
    }

    /**
     * Set the transliterator to with latin will be converted
     *
     * @param string $transliterateTo
     *
     * @throws \InvalidArgumentException
     */
    public function setTransliterateTo($transliterateTo)
    {
        $availableIds = transliterator_list_ids();
        if (!in_array($transliterateTo, $availableIds)) {
            throw new \InvalidArgumentException(sprintf(
                'Inexistant conversion : %s, available conversions are %s',
                $transliterateTo,
                implode(', ', $availableIds)
            ));
        }

        $this->transliterateTo = $transliterateTo;
    }

    /**
     * Get the transliterator instance
     *
     * @return \Transliterator
     */
    protected function getTransliterator()
    {
        if (null === $this->transliterator) {
            $this->transliterator = \Transliterator::create(self::TRANSLITERATE_RULE);
        }

        return $this->transliterator;
    }

    /**
     * Get the inverse transliterator instance
     *
     * @return \Transliterator
     */
    protected function getInverseTransliterator()
    {
        if (null === $this->inverseTransliterator) {
            $this->inverseTransliterator = \Transliterator::create($this->transliterateTo);
        }

        return $this->inverseTransliterator;
    }

    /**
     * Convert non latin string to ascii using transliterator
     *
     * @param string $string
     *
     * @return string
     */
    public function nonLatinToAscii($string)
    {
        if ($this->useTransliterate) {
            $string = $this->getTransliterator()->transliterate($string);
        }

        return $string;
    }

    /**
     * Convert ascii/latin string to target charset using transliterator
     *
     * @param string $string
     *
     * @return string
     */
    public function asciiToNonLatin($string)
    {
        if ($this->useTransliterate) {
            $string = $this->getInverseTransliterator()->transliterate($string);
        }

        return $string;
    }

    /**
     * Get latin alphabetic letters
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
     * @param bool   $transliterate
     * @param string $charset
     *
     * @return string
     */
    public function removeAccents($str, $charset='utf-8')
    {
        if ($this->useTransliterate) {
            $str = $this->nonLatinToAscii($str);
        }

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
     * @param bool   $transliterate
     *
     * @return string
     */
    public function urlSafe($string)
    {
        if ($this->useTransliterate) {
            $string = $this->getTransliterator()->transliterate($string);
        }

        $string = $this->removeAccents($string);
        $string = preg_replace('/[^\w]/', '-', $string);
        return preg_replace('/-{2,}/', '-', $string);
    }
}
