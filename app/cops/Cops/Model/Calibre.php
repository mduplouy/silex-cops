<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Silex\Application as BaseApplication;
use Cops\Model\Author\Collection as AuthorCollection;

/*
 * Calibre model class
 *
 * Provides clones of some Calibre internal functions
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */

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
 * author_name_suffixes = ('Jr', 'Sr', 'Inc', 'Ph.D', 'Phd',
 *                       'MD', 'M.D', 'I', 'II', 'III', 'IV',
 *                       'Junior', 'Senior')
 * author_name_prefixes = ('Mr', 'Mrs', 'Ms', 'Dr', 'Prof')
 * author_name_copywords = ('Corporation', 'Company', 'Co.', 'Agency', 'Council',
 *       'Committee', 'Inc.', 'Institute', 'Society', 'Club', 'Team')
 *
 */

class Calibre
{
    /**
     * Author sort copy algorithm
     *
     * @var string
     */
    private $authorSortMethod;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->authorSortMethod = $app['config']->getValue('author_sort_copy_method');
    }

    /**
     * Get author sort name depending of sort algorithm
     *
     * @param  string $name
     *
     * @return string
     */
    public function getAuthorSortName($name) {
        switch ($this->authorSortMethod) {
            case 'invert':
            default:
                $name = $this->invertName($name);
                break;

            case 'nocomma':
                $name = $this->invertName($name, '');
                break;

            case 'comma':
                $name = $this->commaName($name);
                break;
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

        if (count($authorArray) == 1) {
            return $name;
        }
        $lastName = array_pop($authorArray);
        $firstName = implode(' ', $authorArray);
        return sprintf('%s%s %s', $lastName, $sep, $firstName);
    }

    /**
     * Treat name with comma algorithm
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
}
