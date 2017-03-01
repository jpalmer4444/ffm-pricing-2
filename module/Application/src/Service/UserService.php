<?php
/**
 * @copyright  Copyright (c) 2015 Busteco Global Brain
 * @author     Valentina <valentina@busteco.ro> / Ana-Maria Buliga <anamaria@busteco.ro>
 */

namespace Application\Service;

use User\Entity\User;

/**
 * Class UserService
 * @package Application\Service
 */
class UserService extends BaseService
{
    
    public function __construct(\Doctrine\ORM\EntityManager $entityManager, array $config) {
        parent::__construct($entityManager, $config, User::class);
    }
    
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('u');
    }

    /**
     * Save User
     *
     * @param User $user
     * @param int  $id
     * @return bool
     */
    public function save(User $user, $id = null)
    {
        if ($id == 0 && !is_null($id)) {
            $user->setLastLoginDate(null);
            $user->setCreationDate((new \DateTime()));
        }

        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete User
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var User $user */
            $user = $this->getRepository()->find($id);
            if (!is_null($user)) {
                $user->clearPrinters();
                $user->clearRoles();
                $this->getEntityManager()->remove($user);
                $this->getEntityManager()->flush();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
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
     * Get user by session id
     *
     * @param $sessionId
     * @return null|object
     */
    public function findBySessionId($sessionId)
    {
        return $this->getRepository()->findOneBy(['sessionId' => $sessionId]);
    }
    
    /**
     * Get user by email
     *
     * @param $email
     * @return null|object
     */
    public function findByEmail($email)
    {
        return $this->getRepository()->findOneBy(['email' => $email]);
    }

    /**
     * Get all users, as formatted array
     *
     * @return array
     */
    public function findAllArray()
    {
        $data = $this->getRepository()->findAll();
        $result = [];
        /** @var User $item */
        foreach ($data as $item) {
            $result[$item->getId()] = $item->getUsername();
        }

        return $result;
    }
}
