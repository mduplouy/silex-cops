<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\ImageProcessor\Adapter;

use Cops\Model\ImageProcessor\ImageProcessorInterface;
use Cops\Model\ImageProcessor\ImageProcessorAbstract;


/**
 * Imagick processing class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Imagick extends ImageProcessorAbstract
{

    protected $_width;

    protected $_height;


    public function generateThumbnail($src, $dest, array $params=array())
    {
        $imagick = new \Imagick($src);



    }

    public function resize($width=null, $height=null)
    {
        return $this;
    }
}