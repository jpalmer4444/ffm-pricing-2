<?php
namespace Application\Service;

use Application\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class CustomerService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, Customer::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('customer');
    }

    /**
     * Create Application\Entity\Customer
     *
     * @param $customer
     * @return bool
     */
    public function save($customer)
    {
        try {
            $this->getEntityManager()->persist($customer);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->logger->log(Logger::ERR, $e->getTraceAsString());
            return false;
        }

        return true;
    }
    
    /**
     * Update Application\Entity\Customer
     *
     * @param $customer
     * @return bool
     */
    public function update($customer)
    {
        try {
            $this->getEntityManager()->merge($customer);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\Customer
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var Customer $role */
            $customer = $this->getRepository()->find($id);

            if (!is_null($customer)) {
                $this->getEntityManager()->remove($customer);
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
        /** @var Customer $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getId();
        }

        return $result;
    }
    
}
