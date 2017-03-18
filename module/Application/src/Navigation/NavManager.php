<?php

namespace Application\Navigation;

use Application\Entity\Role;
use Application\Service\UserService;
use Application\View\Helper\Breadcrumbs;
use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\Url;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager {

    /**
     * Auth service.
     * @var AuthenticationService
     */
    private $authService;

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
     * @var Application\View\Helper\Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * Constructs the service.
     */
    public function __construct(
    AuthenticationService $authService, UserService $userService, array $config, Breadcrumbs $breadcrumbs, $urlHelper
    ) {
        $this->authService = $authService;
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

        //BEGIN Authentication/Rendering Logic
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users and any other links 
        // that should be visible by logged-in users.
        if (!$this->authService->hasIdentity()) {

            $items[] = [
                'id' => 'login',
                'label' => 'Sign in',
                'link' => $url('login'),
                'float' => 'right'
            ];
        } else {

            //home page for all users.
            $items[] = [
                'id' => 'customers',
                'label' => 'Customers',
                'float' => 'static',
                'link' => $url('customer', ['action' => 'index'])
            ];

            //only display admin drop down for admin users.
            $isAdmin = FALSE;
            $user = $this->userService->getRepository()->findOneByUsername($this->authService->getIdentity());
            $roles = !empty($user) ? $user->getRoles() : [];
            foreach ($roles as $role) {
                if (strcmp($role->getName(), Role::ROLE_ADMIN) == 0) {
                    $isAdmin = TRUE;
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
