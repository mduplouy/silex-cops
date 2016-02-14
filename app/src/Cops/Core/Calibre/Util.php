<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Calibre;

use Cops\Core\AbstractEntity;
use Cops\Core\Config;

/*
 * Calibre utility
 * Provides clones of some Calibre internal functions
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Util extends AbstractEntity
{
    /**
     * Sort methods
     */
    const SORT_METHOD_INVERT  = 'invert';
    const SORT_METHOD_COMMA   = 'comma';
    const SORT_METHOD_NOCOMMA = 'nocomma';

    /**
     * Author sort copy algorithm
     * @var string
     */
    private $authorSortMethod;

    /**
     * Allowed author sort methods
     * @var array
     */
    private $allowedAuthorSortMethods = array(
        self::SORT_METHOD_INVERT,
        self::SORT_METHOD_COMMA,
        self::SORT_METHOD_NOCOMMA,
    );

    /**
     * Article patterns per language taken from Calibre src
     * @var array
     */
    private $articlePatterns = array(
        // English
        'en' => array('A\s+', 'The\s+', 'An\s+'),

        // French
        'fr' => array('L[ea]\s+', "L['´]", 'Les\s+', 'Une?\s+', 'Des?\s+', 'De\s+La\s+',
            "D('|´)"),

        // German
        'de' => array('De[rsn]\s+', 'Die\s+', 'Das\s+', 'Eine?\s+', 'Eine[nms]\s+',
            'Dem\s+', 'Einem\s+'),

        // Spanish
        'es' => array('El\s+', 'L[ao]s?\s+', 'Un\s+', 'Unas?\s+', 'Unos\s+'),

        // Italian
        'it' => array('Lo\s+', 'Il\s+', "L'", 'La\s+', 'Gli\s+', 'I\s+', 'Le\s+'),

        // Portuguese
        'pt' => array('A\s+', 'O\s+', 'Os\s+', 'As\s+', 'Um\s+', 'Uns\s+', 'Uma\s+', 'Umas\s+'),

        // Romanian
        'ro' => array('Un\s+', 'O\s+', 'Nişte\s+', ),

        // Dutch
        'nl' => array('De\s+', 'Het\s+', 'Een\s+', "'n\s+", "'s\s+", 'Ene\s+', 'Ener\s+',
            'Enes\s+', 'Den\s+', 'Der\s+', 'Des\s+', "'t\s+"),

        // Swedish
        'sw' => array ('En\s+', 'Ett\s+', 'Det\s+', 'Den\s+', 'De\s+', ),

        // Turkish
        'tu' => array('Bir\s+'),

        // Afrikaans
        'af' => array("'n\s+", 'Die\s+'),

        // Greek
        'el' => array('O\s+', 'I\s+', 'To\s+', 'Ta\s+', 'Tus\s+', 'Tis\s+',
            "'Enas\s+", "'Mia\s+", "'Ena\s+", "'Enan\s+", ),

        // Hungarian
        'hu' => array('A\s+', 'Az\s+', 'Egy\s+'),
    );

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->authorSortMethod = $config->getValue('author_sort_copy_method');
    }

    /**
     * Set author sort method
     *
     * @param string $authorSortMethod
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setAuthorSortMethod($authorSortMethod)
    {
        if (!in_array($authorSortMethod, $this->allowedAuthorSortMethods)) {
            throw new \InvalidArgumentException(
                sprintf('Unavailable sort method %s', $authorSortMethod)
            );
        }

        $this->authorSortMethod = $authorSortMethod;

        return $this;
    }

    /**
     * Author sort name algorithm
     * The algorithm used to copy author to author_sort
     * Possible values are:
     *  invert: use "fn ln" -> "ln, fn"
     *  copy  : copy author to author_sort without modification
     *  comma : use 'copy' if there is a ',' in the name, otherwise use 'invert'
     *  nocomma : "fn ln" -> "ln fn" (without the comma)
     *
     * When this tweak is changed, the author_sort values stored with each author
     * must be recomputed by right-clicking on an author in the left-hand tags pane,
     * selecting 'manage authors', and pressing 'Recalculate all author sort values'.
     * The author name suffixes are words that are ignored when they occur at the
     * end of an author name. The case of the suffix is ignored and trailing
     * periods are automatically handled. The same is true for prefixes.
     * The author name copy words are a set of words which if they occur in an
     * author name cause the automatically generated author sort string to be
     * identical to the author name. This means that the sort for a string like Acme
     * Inc. will be Acme Inc. instead of Inc., Acme
     *
     * author_sort_copy_method = 'comma'
     * author_name_suffixes = ('J', 'S', 'Inc', 'Ph.D', 'Phd',
     *                       'MD', 'M.D', 'I', 'II', 'III', 'IV',
     *                       'Junio', 'Senio')
     * author_name_prefixes = ('M', 'Mrs', 'Ms', 'D', 'Prof')
     * author_name_copywords = ('Corporation', 'Company', 'Co.', 'Agency', 'Council',
     *       'Committee', 'Inc.', 'Institute', 'Society', 'Club', 'Team')
     *
     *
     * Get author sort name depending of sort algorithm
     *
     * @param  string $name
     *
     * @return string
     */
    public function getAuthorSortName($name)
    {
        switch ($this->authorSortMethod) {
            case 'nocomma':
                $name = $this->invertName($name, '');
                break;

            case 'comma':
                $name = $this->commaName($name);
                break;

            case 'invert':
            default:
                $name = $this->invertName($name);
        }

        return $name;
    }

    /**
     * Treat name with invert algorithm
     *
     * @param  string $name The input name
     * @param  string $sep  The separator to use (,)
     *
     * @return string
     */
    private function invertName($name, $sep = ',')
    {
        $name = trim($name);
        $authorArray = explode(' ', $name);

        if (count($authorArray) > 1) {
            $name = sprintf('%s%s %s', array_pop($authorArray), $sep, implode(' ', $authorArray));
        }

        return $name;
    }

    /**
     * Process name with comma algorithm
     *
     * @param  string $name
     *
     * @return string
     */
    private function commaName($name)
    {
        if (strpos($name, ',') === false) {
            $name = $this->invertName($name);
        }

        return $name;
    }

    /**
     * Modify title_sort so articles are placed at the end
     *
     * @param string       $title    Title to be modified
     * @param string|false $langCode Book ISO 2 language code
     *
     * @return string
     */
    public function getTitleSort($title, $langCode = false)
    {
        if ($langCode !== false) {
            $patterns = $this->articlePatterns[$langCode];
        } else {
            $patterns = $this->articlePatterns['en'];
        }

        $titleSort = $title;
        foreach($patterns as $pattern) {
            $pattern = sprintf('/^(%s)(.*)/i', $pattern);
            $titleSort = preg_replace($pattern, '\2, \1', $title, -1, $count);
            if ($count > 0) {
                break;
            }
        }

        return trim($titleSort);
    }

    /**
     * Return specific DB user functions for DB container
     *
     * @return array
     */
    public function getDBInternalFunctions()
    {
        return array(
            'userDefinedFunctions' => array(
                'title_sort' => array(
                    // implement title_sort function from calibre
                    'callback' => function($title) {
                        return $title;
                    },
                    'numArgs' => 1
                ),
            ),
        );
    }

    /**
     * Trigger getter
     *
     * @return array
     */
    public function getTriggers()
    {
        return $this->getRepository()->getTriggers();
    }

    /**
     * Load existing triggers inside DB
     *
     * @return array
     */
    public function loadExistingTriggers()
    {
        return $this->getRepository()->loadTriggersFromDb();
    }
}
