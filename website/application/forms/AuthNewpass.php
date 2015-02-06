<?php
    class Application_Form_AuthNewpass extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('authnewpassForm');
          
          $email = new Zend_Form_Element_Text('email');
          $email->setAttrib('size', '50');
          $email->setLabel('Email')
             ->setRequired(true);
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('authnewpassForm');   
             
          $button = new Zend_Form_Element_Submit('submit');
          $button->setLabel(' SEND NEW ');
          
          $this->addElements(array($email, $hidden, $button));
          $this->setMethod('post');
       }
    }