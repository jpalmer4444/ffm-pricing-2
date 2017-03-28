<?php

namespace Application\Service;

use Application\Entity\Permission;
use Application\Service\BaseService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Zend\Log\Logger;
/**
 * @copyright  Copyright (c) 2017 Fulton Inc
 * @author     Jason Palmer <jpalmer@meadedigital.com>
 */

class PermissionService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, Permission::class);
    }
    
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('permission');
    }

    /**
     * @param $id
     * @return null|object
     */
    public function findById($id)
    {
        return $this->getRepository()->find($id);
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
        $query = $this->getEntityManager()->createQuery(empty($dql) ? $this->config['pricing_config']['dql']['find_eager']['PermissionService'] : $dql);
        foreach ($eager as $clazz => $alias) {
            $query->setFetchMode($clazz, $alias, ClassMetadata::FETCH_EAGER);
        }
        foreach ($parameters as $parameter => $value) {
            $query->setParameter($parameter, $value);
        }
        return $query->getResult();
    }

    /**
     * @return array
     */
    public function findAllGrouped()
    {
        $permissions = $this->getRepository()->findAllExceptLogin();
        $result = [];
        /** @var Permission $perm */
        foreach ($permissions as $perm) {
            if (!strpos($perm->getName(), '/')) {
                $result[$perm->getName()]['title'] = $perm->getTitle();
                $result[$perm->getName()]['url'] = $perm->getName();
                $result[$perm->getName()]['id'] = $perm->getId();
            } else {
                $parentName = explode('/', $perm->getName(), 2)[0];
                $result[$parentName]['childs'][$perm->getId()]['url'] = $perm->getName();
                $result[$parentName]['childs'][$perm->getId()]['title'] = $perm->getTitle();
                $result[$parentName]['childs'][$perm->getId()]['id'] = $perm->getId();
            }
        }

        return $result;
    }

    /**
     * Save permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function save(Permission $permission)
    {
        try {
            $this->getEntityManager()->persist($permission);
            $this->getEntityManager()->flush();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete Permission
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var Permission $permission */
            $permission = $this->getRepository()->find($id);

            if (!is_null($permission) && empty($permission->getRoles())) {

                $this->getEntityManager()->remove($permission);
                $this->getEntityManager()->flush();

                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}
