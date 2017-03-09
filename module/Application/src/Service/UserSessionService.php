<?php
namespace Application\Service;

use Application\Entity\UserSession;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zend\Log\Logger;

class UserSessionService extends BaseService
{
    
    public function __construct(EntityManager $entityManager, array $config, Logger $logger) {
        parent::__construct($entityManager, $config, $logger, UserSession::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('userSession');
    }

    /**
     * Update / Edit Application\Entity\UserSession
     *
     * @param $userSession
     * @return bool
     */
    public function save($userSession)
    {
        try {
            $this->getEntityManager()->persist($userSession);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Delete Application\Entity\UserSession
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            /** @var UserSession $role */
            $userSession = $this->getRepository()->find($id);

            if (!is_null($userSession)) {
                $this->getEntityManager()->remove($userSession);
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
        /** @var UserSession $item */
        foreach ($data as $item) {
            $result[$item->getSessionId()] = $item->getSessionId();
        }

        return $result;
    }
}
