<?php
namespace Application\Service;

use Application\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class ProductService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, Product::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('product');
    }

    /**
     * Update Edit Application\Entity\Product
     *
     * @param $product
     * @return bool
     */
    public function save($product)
    {
        try {
            $this->getEntityManager()->persist($product);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\Product
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var Product $role */
            $customer = $this->getRepository()->find($id);

            if (!is_null($product)) {
                $this->getEntityManager()->remove($product);
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
        /** @var Product $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getId();
        }

        return $result;
    }
    
}