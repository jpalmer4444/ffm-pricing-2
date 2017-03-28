<?php

namespace Application\Form;

use Application\Validator\RegexValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Description of ProductForm
 *
 * @author jasonpalmer
 */
class ProductForm extends Form {

    /**
     * Constructor.     
     */
    public function __construct() {
        // Define form name
        parent::__construct('product-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() {
        // Add "product" field
        $this->add([
            'type' => 'text',
            'name' => 'product',
            'options' => [
                'label' => 'Product',
            ],
        ]);

        // Add "description" field
        $this->add([
            'type' => 'text',
            'name' => 'description',
            'options' => [
                'label' => 'Description',
            ],
        ]);

        // Add "comment" field
        $this->add([
            'type' => 'text',
            'name' => 'comment',
            'options' => [
                'label' => 'Comment',
            ],
        ]);
        
        // Add "overrideprice" field
        $this->add([
            'type' => 'text',
            'name' => 'overrideprice',
            'options' => [
                'label' => 'Override Price',
            ],
        ]);

        // Add "uom" field
        $this->add([
            'type' => 'text',
            'name' => 'uom',
            'options' => [
                'label' => 'UOM',
            ],
        ]);

        // Add "sku" field
        $this->add([
            'type' => 'text',
            'name' => 'sku',
            'options' => [
                'label' => 'SKU',
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter(){
        
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        
        // Add filter for "product" field
        $inputFilter->add([
            'name' => 'product',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255
                    ],
                ],
            ],
        ]);
        
        // Add filter for "description" field
        $inputFilter->add([
            'name' => 'description',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255
                    ],
                ],
            ],
        ]);
        
        // Add filter for "comment" field
        $inputFilter->add([
            'name' => 'comment',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255
                    ],
                ],
            ],
        ]);
        
        // Add filter for "overrideprice" field
        $inputFilter->add([
            'name' => 'overrideprice',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => RegexValidator::class,
                        'options' => [
                            'pattern' => '/^(?=.)(?!\$$)(([1-9][0-9]{0,2}(,[0-9]{3})*)|[0-9]+)?(\.[0-9]{2})?$/',
                            'message' => 'You must enter a valid dollar amount.'
                        ],
                    ],                    
                ],
        ]);
        
        // Add filter for "uom" field
        $inputFilter->add([
            'name' => 'uom',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255
                    ],
                ],
            ],
        ]);
        
        // Add filter for "sku" field
        $inputFilter->add([
            'name' => 'sku',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => RegexValidator::class,
                        'options' => [
                            'pattern' => '/^[a-z0-9A-Z\-]{1,20}$/',
                            'message' => 'You must enter a valid sku (alpha-numeric).'
                        ],
                    ],                    
                ],
        ]);
        
    }
}
