<?php
    class Application_Form_AuthChange extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('authchangeForm');
          
          $change = new Zend_Form_Element_Text('change');
          $change->setLabel('New Password')
             ->setRequired(true);
          
          $verify = new Zend_Form_Element_Text('verify');
          $verify->setLabel('Re-enter New')
             ->setRequired(true);
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('authchangeForm');   
             
          $button = new Zend_Form_Element_Submit('submit');
          $button->setLabel('SUBMIT');
          
          $continue = new Zend_Form_Element_Hidden('continue');
          
          $this->addElements(array($change, $verify, $hidden, $button, $continue));
          $this->setMethod('post');
       }
    }