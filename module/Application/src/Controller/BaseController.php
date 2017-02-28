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
