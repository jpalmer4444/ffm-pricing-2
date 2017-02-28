<?php

/*
 * BaseController parent for all application controller to encapsulate common functionality.
 * 1. Authentication
 * 2. Views
 */

namespace Application\Controller;

use Application\Service\UserService;
use DateTime;
use FultonFishMarket\Entity\User;
use InvalidArgumentException;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use ZfcRbac\Exception\UnauthorizedException;

/**
 * Description of BaseController
 *
 * @author jasonpalmer
 */
abstract class BaseController extends AbstractActionController{
    
    /**
     *
     * @var Adapter
     */
    protected $dbAdapter;

    protected function getBasePath()
    {
        return BASE_PATH.'/';
    }

    protected function getBaseUrl()
    {

        $uri = $this->getRequest()->getUri();

        return sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
    }

    /**
     * Check if user has permissions to access current route
     * @param MvcEvent $e
     * @return mixed
     * @throws UnauthorizedException
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $this->getEvent()->getRouteMatch()->getMatchedRouteName();

        if (is_null($this->getCurrentUser())) {
            
        } else {
            
        }

        parent::onDispatch($e);
    }

    /**
     * Get logged user
     *
     * @return User
     */
    protected function getCurrentUser()
    {
        

        return null;
    }

    /**
     * @return ViewModel
     */
    public function getView()
    {
        $view = new ViewModel();
        $view->user = $this->getCurrentUser();

        return $view;
    }

    public function htmlResponse($html)
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($html);
        return $response;
    }

    public function jsonResponse($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('$data param must be array');
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($data));
        return $response;
    }

    /**
     *
     * @return Adapter
     */
    protected function getDbAdapter()
    {
        if (!$this->dbAdapter) {
            $this->dbAdapter = $this->getZFDBAdapter();
        }

        return $this->dbAdapter;
    }
    
}
