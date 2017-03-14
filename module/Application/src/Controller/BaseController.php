<?php

/*
 * BaseController parent for all application controller to encapsulate common functionality.
 * 1. Authentication
 * 2. Views
 */

namespace Application\Controller;

use InvalidArgumentException;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of BaseController
 *
 * @author jasonpalmer
 */
abstract class BaseController extends AbstractActionController{
    
    protected $config;
    
    protected $authenticationService;
    
    public function __construct(AuthenticationService $authenticationService, array $config){
        $this->config = $config;
        $this->authenticationService = $authenticationService;
    }

    protected function getBasePath()
    {
        return BASE_PATH.'/';
    }

    protected function getBaseUrl()
    {

        $uri = $this->getRequest()->getUri();

        return sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
    }
    
    public function serveNgPage($isNgPage = TRUE) {
        $this->layout()->setVariable('ngPage', $isNgPage);
        $this->layout()->setVariable('username', $this->authenticationService->getIdentity());
        $this->layout()->setVariable('loginUrl', $this->config['ngSettings']['loginUrl']);
        $this->layout()->setVariable('usersTableAjax', $this->config['ngSettings']['usersTableAjax']);
        $this->layout()->setVariable('usersTableUpdateStatusAjax', $this->config['ngSettings']['usersTableUpdateStatusAjax']);
    }

    /**
     * @return ViewModel
     */
    public function getView($data = NULL)
    {
        $view = !empty($data) ? new ViewModel($data) : new ViewModel();
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
    
}
