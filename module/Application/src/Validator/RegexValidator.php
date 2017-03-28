<?php

namespace Application\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

/**
 * This validator class is designed for checking if there is an existing user 
 * with such an email.
 */
class RegexValidator extends AbstractValidator {

    protected $options = array(
        'pattern' => null,
        'message' => null
    );

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        "invalid" => "Value is invalid"
    );

    /**
     * Constructor.     
     */
    public function __construct($options = null) {

        if (is_array($options)) {
            if (isset($options['pattern']))
                $this->options['pattern'] = $options['pattern'];
            if (isset($options['message']))
                $this->options['message'] = $options['message'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if user exists.
     */
    public function isValid($value) {

        $validator = new Regex($this->options['pattern']);

        $valid =  $validator->isValid($value);
        
        if(!$valid){
            $this->error($this->options['message']);
        }
        
        return $valid;
    }

}
