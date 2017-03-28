<?php

namespace Application\Service;

use Application\Entity\Checkbox;
use Application\Service\BaseService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class CheckboxService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, Checkbox::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('checkbox');
    }

    /**
     * Update Edit Application\Entity\Checkbox
     *
     * @param $checkbox
     * @return bool
     */
    public function save($checkbox)
    {
        try {
            $this->getEntityManager()->persist($checkbox);
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\Checkbox
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var Checkbox $role */
            $checkbox = $this->getRepository()->find($id);

            if (!is_null($checkbox)) {
                $this->getEntityManager()->remove($checkbox);
                $this->getEntityManager()->flush();

                return true;
            }
        } catch (Exception $e) {
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
    
    /**
     * 
     * @param array $eager map of class names to alias for eagerly fetched @ManyToOne and @OneToOne associations only
     * @param array $parameters map of parameters names to values for the passed in SQL @default = []
     * @param type $dql DQL to execute
     */
    public function findEager(array $eager, array $parameters = [], $dql = []) {
        /** @var Query $item */
        $query = $this->getEntityManager()->createQuery(empty($dql) ? $this->config['pricing_config']['dql']['find_eager']['CheckboxService'] : $dql);
        foreach ($eager as $clazz => $alias) {
            $query->setFetchMode($clazz, $alias, ClassMetadata::FETCH_EAGER);
        }
        foreach ($parameters as $parameter => $value) {
            $query->setParameter($parameter, $value);
        }
        return $query->getResult();
    }

    public function findAllArray()
    {
        $data = $this->getRepository()->findAll();
        $result = [];
        /** @var Checkbox $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getId();
        }

        return $result;
    }
    
}
