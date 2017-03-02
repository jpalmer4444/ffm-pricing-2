<?php
/**
 * @copyright  Copyright (c) 2015 Busteco Global Brain
 * @author     Ana-Maria Buliga <anamaria@busteco.ro>
 */

namespace Application\Service;

use Application\Entity\Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Zend\Log\Logger;

class RolesService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, Role::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('p');
    }

    /**
     * Update / Edit Role
     *
     * @param $role
     * @return bool
     */
    public function save($role)
    {
        try {
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Role
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var Role $role */
            $role = $this->getRepository()->find($id);

            if (!is_null($role) && empty($role->getUsers())) {
                $role->clearPermissions();
                $this->getEntityManager()->remove($role);
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

    public function findAllArray()
    {
        $data = $this->getRepository()->findAll();
        $result = [];
        /** @var Role $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getName();
        }

        return $result;
    }
}
