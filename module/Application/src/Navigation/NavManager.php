<?php

namespace Application\Navigation;

use Application\Service\UserService;
use Application\View\Helper\Breadcrumbs;
use User\Service\AuthManager;
use Zend\View\Helper\Url;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager {

    /**
     * Auth service.
     * @var AuthManager
     */
    private $authManager;

    /**
     * Url view helper.
     * @var Url
     */
    private $urlHelper;

    /**
     * Application\Service\UserService
     * @var userService
     */
    private $userService;

    /**
     *
     * @var array
     */
    private $config;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * Constructs the service.
     */
    public function __construct(
    AuthManager $authManager, UserService $userService, array $config, Breadcrumbs $breadcrumbs, $urlHelper
    ) {
        $this->authManager = $authManager;
        $this->urlHelper = $urlHelper;
        $this->userService = $userService;
        $this->config = $config;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems() {

        $url = $this->urlHelper;
        $items = [];

        //Links Visible always to everyone logged in or not.
        //default Home page - no login required. 

        /* $items[] = [
          'id' => 'home',
          'label' => 'Home',
          'link' => $url('home')
          ]; */

        //add links here for pages that will be visible for all users logged-in or not.
        //you must adjust User\Module.php (~line 85) onDispatch method to ignore calls for 
        //the associated Controller and action or you will end up with an infinite loop.
        /*
          $items[] = [
          'id' => 'about',
          'label' => 'About',
          'link' => $url('about')
          ];
         */

        $user = $this->authManager->getLoggedInUser();

        //BEGIN Authentication/Rendering Logic
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users and any other links 
        // that should be visible by logged-in users.
        if (!$user) {

            $items[] = [
                'id' => 'login',
                'label' => 'Sign in',
                'link' => $url('login'),
                'float' => 'right'
            ];
        } else {

            //render Customers link for all users with sales_attr_id value in users table
            if (!empty($user->getSales_attr_id())) {
                $items[] = [
                    'id' => 'customers',
                    'label' => 'Customers',
                    'data-ffm-salesperson' => $user->getFullName(),
                    'float' => 'static',
                    'link' => $url('customer', ['action' => 'index', 'id' => $user->getSales_attr_id()])
                ];
            }
            //only display admin drop down for admin users.
            $isAdmin = $this->authManager->isAdmin();
            if ($isAdmin) {
                $items[] = [
                    'id' => 'admin',
                    'label' => '<i class="ion-gear-a"></i>',
                    'float' => 'right',
                    'dropdown' => [
                        [
                            'id' => 'users',
                            'label' => 'Manage Users',
                            'link' => $url('users')
                        ]
                    ]
                ];
            }

            $settingsDropDownManageAccount = [
                'id' => 'manage_account',
                'label' => 'Manage Account',
                'link' => $url('users', ['action' => 'edit', 'id' => $user->getId()])
            ];

            $settingsDropDownViewAccount = [
                'id' => 'view_account',
                'label' => 'View Account',
                'link' => $url('application', ['action' => 'settings'])
            ];

            $settingsDropDownLogout = [
                'id' => 'logout',
                'label' => 'Logout',
                'link' => $url('logout')
            ];

            $settingsDropDown = [];

            //only add the Manage Account link for Admins.
            if ($isAdmin) {
                $settingsDropDown[] = $settingsDropDownManageAccount;
            }

            $settingsDropDown[] = $settingsDropDownViewAccount;

            $settingsDropDown[] = $settingsDropDownLogout;

            $items[] = [
                'id' => 'settings',
                'label' => '<i class="ion-person"></i>' . $user->getUsername(),
                'float' => 'right',
                'dropdown' => $settingsDropDown
            ];

            $loggedInItems = $this->getLoggedInItems();

            if ($loggedInItems)
                array_merge($items, $loggedInItems);

            // add items to right of settings in top right corner only for logged-in users here
            //Show Salespeople link only to Admin users
            if ($isAdmin) {

                $items[] = [
                    'id' => 'salespeople',
                    'label' => 'Salespeople',
                    'float' => 'static',
                    'link' => $url('salespeople', ['action' => 'index']),
                ];
            }
        }

        return $items;
    }

    function getLoggedInItems() {
        return NULL;
    }

}
