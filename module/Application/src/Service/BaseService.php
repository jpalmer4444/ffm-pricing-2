<?php

/*
 * Base Service class
 */

namespace Application\Service;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\Config;
use ZendPdf\Resource\Font\AbstractFont;
use Doctrine\ORM\EntityManager;

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
     * @var Config
     */
    protected $config;
    
    public function __construct(EntityManager $entityManager, array $config, $clazz) {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($clazz);
    }

    /**
     * Retrieve entity repository instance
     *
     * @return EntityRepository
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * Set entity repository instance
     *
     * @param EntityRepository $repository
     * @return $this
     */
    public function setRepository(EntityRepository $repository) {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Retrieve entity manager instance
     *
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * Set entity manager instance
     *
     * @param EntityManager $entityManager
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager) {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Get text width, used for align center, in pdf document
     *
     * @param               $text
     * @param AbstractFont  $font
     * @param               $fontSize
     * @return float
     */
    protected function getTextWidth($text, AbstractFont $font, $fontSize) {
        $drawingText = $text; //iconv ( '', $encoding, $text );
        $characters = array();
        for ($i = 0; $i < strlen($drawingText); $i ++) {
            $characters[] = ord($drawingText[$i]);
        }

        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);

        $textWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;

        return $textWidth;
    }

    /**
     * 
     * @param type $message
     * @param type $level
     */
    protected function logMessage($message, $level = Zend\Log\Logger::INFO) {
        if (!$this->logger) {
            $this->logger = new Logger;
            $writer = new Stream(__DIR__ . '/../../../../data/log/error.out');
            $this->logger->addWriter($writer);
        }
        $this->logger->log($level, $message);
    }

    /**
     * Get formatted datetime for now
     *
     * @return string
     */
    protected function getDatetime() {
        return (new DateTime())->format('Y-m-d H:i:s');
    }

}
