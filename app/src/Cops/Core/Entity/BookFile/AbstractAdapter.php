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

use Cops\Core\AbstractEntity;
use Cops\Core\CollectionableInterface;
use Cops\Core\Translator;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractAdapter extends AbstractEntity implements CollectionableInterface
{
    /**
     * Bookfile ID
     * @var int
     */
    protected $id;

    /**
     * Book ID
     * @var int
     */
    protected $bookId;

    /**
     * Bookfile format
     * @var string
     */
    protected $format;

    /**
     * File size in bytes
     * @var int
     */
    protected $uncompressedSize = 0;

    /**
     * File name without extension
     * @var string
     */
    protected $name;

    /**
     * Bookfile directory
     * @var string
     */
    protected $directory;

    /**
     * Storage directory
     * @var string
     */
    protected $storageDir;

    /**
     * Translator instance
     * @var Translator
     */
    protected $translator;

    /**
     * Set translator
     *
     * @param Translator $translator
     *
     * @return self
     */
    public function setTranslator(Translator $translator)
    {
       $this->translator = $translator;

       return $this;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bookId
     *
     * @param int $bookId
     *
     * @return self
     */
    public function setBookId($bookId)
    {
        $this->bookId = (int)$bookId;

        return $this;
    }

    /**
     * Get bookId
     *
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set uncompressedSize
     *
     * @param int $uncompressedSize
     *
     * @return self
     */
    public function setUncompressedSize($uncompressedSize)
    {
        $this->uncompressedSize = (int) $uncompressedSize;

        return $this;
    }

    /**
     * Get uncompressedSize
     *
     * @return int
     */
    public function getUncompressedSize()
    {
        return $this->uncompressedSize;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set directory
     *
     * @param string $directory
     *
     * @return self
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
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
     * Get storageDir
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getStorageDir()
    {
        if (null === $this->storageDir) {
            throw new \RuntimeException('Storage dir is not set');
        }

        return $this->storageDir;
    }

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getStorageDir() . DS
            . $this->directory . DS
            . $this->name . '.'
            . strtolower($this->format);
    }

    /**
     * Get translated human readable file size
     *
     * @return string
     */
    public function getFormattedSize()
    {
        $size = $this->uncompressedSize;
        $label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
        $labelCount = count($label);
        for ($i = 0; $size >= 1024 && $i < ($labelCount -1); $size /= 1024, $i++);
        return round($size, $i-1) . ' ' . $this->translator->trans($label[$i]);
    }

    /**
     * Get file name with extension
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->name.'.'.strtolower($this->format);
    }
}
