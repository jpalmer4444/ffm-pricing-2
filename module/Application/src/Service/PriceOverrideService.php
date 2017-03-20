<?php
namespace Application\Service;

use Application\Entity\PriceOverride;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class PriceOverrideService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, ItemPriceOverride::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('priceOverride');
    }

    /**
     * Update Edit Application\Entity\PriceOverride
     *
     * @param $priceOverride
     * @return bool
     */
    public function save($priceOverride)
    {
        try {
            $this->getEntityManager()->persist($priceOverride);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\PriceOverride
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var PriceOverride $role */
            $priceOverride = $this->getRepository()->find($id);

            if (!is_null($priceOverride)) {
                $this->getEntityManager()->remove($priceOverride);
                $this->getEntityManager()->flush();

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @param $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function findAllArray()
    {
        $data = $this->getRepository()->findAll();
        $result = [];
        /** @var PriceOverride $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getId();
        }

        return $result;
    }
    
}
