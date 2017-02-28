<?php

/*
 * Base Service class
 */

namespace Application\Service;

use DateTime;
use Doctrine\ORM\EntityRepository;
use DoctrineORMModule\Options\EntityManager;
use Zend\Log\Formatter\Simple;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\Config;
use ZendPdf\Resource\Font\AbstractFont;
use ZfTable\AbstractTable;

/**
 * Description of BaseService
 *
 * @author jasonpalmer
 */
class BaseService {
    
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var AbstractTable
     */
    protected $table;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Retrieve entity repository instance
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set entity repository instance
     *
     * @param EntityRepository $repository
     * @return $this
     */
    public function setRepository(EntityRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Retrieve entity manager instance
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set entity manager instance
     *
     * @param EntityManager $entityManager
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return AbstractTable
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param AbstractTable $table
     */
    public function setTable(AbstractTable $table)
    {
        $this->table = $table;
    }

    /**
     * Get text width, used for align center, in pdf document
     *
     * @param               $text
     * @param AbstractFont  $font
     * @param               $fontSize
     * @return float
     */
    protected function getTextWidth($text, AbstractFont $font, $fontSize)
    {
        $drawingText = $text;//iconv ( '', $encoding, $text );
        $characters = array ();
        for ($i = 0; $i < strlen($drawingText); $i ++) {
            $characters[] = ord($drawingText[$i]);
        }

        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);

        $textWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;

        return $textWidth;
    }

    /**
     * @param string $message
     * @param string $path
     */
    protected function logError($message, $path)
    {
        $writer = new Stream($path);
        $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
        $formatter = new Simple($format);
        $writer->setFormatter($formatter);
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->err($message);
    }

    /**
     * Get formatted datetime for now
     *
     * @return string
     */
    protected function getDatetime()
    {
        return (new DateTime())->format('Y-m-d H:i:s');
    }
    
    
}
