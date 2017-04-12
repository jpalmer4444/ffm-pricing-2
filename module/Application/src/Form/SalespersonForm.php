<?php

namespace Application\Form;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use User\Validator\UserEmailExistsValidator;
use User\Validator\UserUsernameExistsValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Description of SalespersonForm
 * 
 * username
 * email
 * full_name
 * status
 * phone
 * password
 *
 * @author jasonpalmer
 */
class SalespersonForm extends Form {

    /**
     * Scenario ('create' or 'update').
     * @var string 
     */
    private $scenario;

    /**
     * Entity manager.
     * @var EntityManager 
     */
    private $entityManager = null;

    /**
     * Current user.
     * @var User 
     */
    private $user = null;

    /**
     * Constructor.     
     */
    public function __construct($scenario = 'create', $entityManager = null, $user = null) {
        // Define form name
        parent::__construct('salesperson-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->user = $user;

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() {
        // Add "username" field
        $this->add([
            'type' => 'text',
            'name' => 'username',
            'required' => true,
            'options' => [
                'label' => 'Username',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type' => 'text',
            'name' => 'email',
            'required' => true,
            'options' => [
                'label' => 'E-mail',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Full Name',
            ],
        ]);
        
        // Add "password" field
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        // Add "confirm_password" field
        $this->add([
            'type' => 'password',
            'name' => 'password_verify',
            'options' => [
                'label' => 'Confirm password',
            ],
        ]);


        $this->add(array(
            'name' => 'phone1',
            'type' => 'Application\Form\Input\Phone',
        ));

        // Add "status" field
        $this->add([
            'type' => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    1 => 'Active',
                    0 => 'Inactive',
                ]
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create'
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $usernameValidators = [
            [
                'name' => 'StringLength',
                'options' => [
                    'min' => 1,
                    'max' => 128
                ],
            ],
        ];

        $emailValidators = [
            [
                'name' => 'StringLength',
                'options' => [
                    'min' => 1,
                    'max' => 128
                ],
            ],
            [
                'name' => 'EmailAddress',
                'options' => [
                    'allow' => Hostname::ALLOW_DNS,
                    'useMxCheck' => false,
                ],
            ],
        ];

        if ($this->scenario == 'create') {

            //add UserExistsValidator on create ONLY

            $usernameValidators [] = [
                'name' => UserUsernameExistsValidator::class,
                'options' => [
                    'entityManager' => $this->entityManager,
                    'user' => $this->user
                ],
            ];

            //add EmailExistsValidator on create only
            $emailValidators [] = [
                'name' => UserEmailExistsValidator::class,
                'options' => [
                    'entityManager' => $this->entityManager,
                    'user' => $this->user
                ],
            ];
        }

        // Add input for "username" field
        $inputFilter->add([
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => $usernameValidators,
        ]);

        // Add input for "email" field
        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => $emailValidators,
        ]);

        // Add input for "full_name" field
        $inputFilter->add([
            'name' => 'full_name',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 512
                    ],
                ],
            ],
        ]);

        if ($this->scenario == 'create') {

            // Add input for "password" field
            $inputFilter->add([
                'name' => 'password',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);

            // Add input for "confirm_password" field
            $inputFilter->add([
                'name' => 'password_verify',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ]);
        } else {

            //password is optional if not scenario is not create
            // Add input for "password" field
            $inputFilter->add([
                'name' => 'password',
                'required' => false,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);

            // Add input for "confirm_password" field
            $inputFilter->add([
                'name' => 'password_verify',
                'required' => false,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ]);
        }

        // Add input for "status" field
        $inputFilter->add([
            'name' => 'status',
            'required' => true,
            'filters' => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'InArray', 'options' => ['haystack' => [1, 0]]]
            ],
        ]);
    }

}
