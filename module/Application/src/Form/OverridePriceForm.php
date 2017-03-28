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
class OverridePriceForm extends Form {

    /**
     * Constructor.     
     */
    public function __construct() {
        // Define form name
        parent::__construct('override-price-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() {
        // Add "overrideprice" field
        $this->add([
            'type' => 'text',
            'name' => 'overrideprice',
            'options' => [
                'label' => 'Override Price',
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
    }
}
