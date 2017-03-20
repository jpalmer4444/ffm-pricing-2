<?php
namespace Application\Service;

use Application\Entity\AddedProduct;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class AddedProductService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, RowPlusItemsPage::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('addedProduct');
    }

    /**
     * Update Edit Application\Entity\AddedProduct
     *
     * @param $addedProduct
     * @return bool
     */
    public function save($addedProduct)
    {
        try {
            $this->getEntityManager()->persist($addedProduct);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\AddedProduct
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var AddedProduct $role */
            $addedProduct = $this->getRepository()->find($id);

            if (!is_null($addedProduct)) {
                $this->getEntityManager()->remove($addedProduct);
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
        /** @var AddedProduct $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getId();
        }

        return $result;
    }
    
}
