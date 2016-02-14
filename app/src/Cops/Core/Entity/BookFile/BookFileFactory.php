<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\BookFile;

use Cops\Core\AbstractFactory;
use Cops\Core\Translator;

/**
 * Book file factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFileFactory extends AbstractFactory
{
    /**
     * Translator instance
     * @var Translator
     */
    private $translator;

    /**
     * Storage directory
     * @var string
     */
    private $storageDir;

    /**
     * Instance getter
     *
     * @param string $instance
     *
     * @return \Cops\Core\Entity\BookFile\AdapterInterface
     *
     * @throws AdapterNotFoundException
     */
    public function getInstance($instance = 'EPUB')
    {
        $adapter = parent::getInstance(strtolower($instance));

        if (!$adapter instanceof AdapterInterface) {
            throw new WrongAdapterException(
                'BookFile adapter must implement \Cops\Core\Entity\BookFile\BookFileInterface'
            );
        }

        return $adapter
            ->setTranslator($this->translator)
            ->setStorageDir($this->storageDir);
    }

    /**
     * Set storageDir
     *
     * @param string $storageDir
     *
     * @return self
     */
    public function setStorageDir($storageDir)
    {
        $this->storageDir = $storageDir;

        return $this;
    }

    /**
     * Set translator
     *
     * @param  Translator $translator
     *
     * @return $this
     */
     public function setTranslator(Translator $translator)
     {
        $this->translator = $translator;

        return $this;
     }
}
