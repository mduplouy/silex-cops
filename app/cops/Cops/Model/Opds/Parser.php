<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Opds;

/**
 * Opds parser class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Parser
{
    public function __construct()
    {
        $curlHandle = curl_init('http://opds.oreilly.com/opds/alpha');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);

        $xml = curl_exec($curlHandle);

        $dom = new \DOMDocument;
        $dom->loadXML($xml);

        $links = $dom->getElementsByTagName('link');

        foreach($links as $link) {
            $type = $link->getAttribute('type');

            if ($type != 'application/atom+xml;profile=opds-catalog;kind=navigation') {
                continue;
            }

            var_dump($link->getAttribute('href'));
            var_dump($type);

        }

    }

}
