<?php
    class Application_Form_AuthLogin extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('authloginForm');
          
          $email = new Zend_Form_Element_Text('email');
          $email->setAttrib('size', '50');
          $email->setLabel('Email')
             ->setRequired(true);
          
          $passwd = new Zend_Form_Element_Password('passwd');
          $passwd->setLabel('Password')
             ->setRequired(true);
          
          $remember = new Zend_Form_Element_Checkbox('remember');
          $remember->setLabel('');
          $remember->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'dd')),
                 ));
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('loginForm');   
          
          $dummy = new Zend_Form_Element_Hidden('dummy');
             
          $button = new Zend_Form_Element_Submit('submit');
          $button->setLabel(' LOGIN ');
          
          $this->addElements(array($email, $passwd, $hidden, $remember, $dummy, $button));
          $this->setMethod('post');
       }
    }