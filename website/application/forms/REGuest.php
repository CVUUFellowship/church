<?php
    class Application_Form_REGuest extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reguestForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reguest');

          $firstname = new Zend_Form_Element_Text('firstname');
          $firstname->setLabel('First name');
          $firstname->setAttrib('size', '40');

          $lastname = new Zend_Form_Element_Text('lastname');
          $lastname->setLabel('Last name');
          $lastname->setAttrib('size', '40');

             
          $dummy = new Zend_Form_Element_Hidden('dummy');

          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');

          $this->addElements(array($hidden,
              $firstname, $lastname,
              $hidden, $dummy, $ebutton, 
              ));

          $this->setMethod('post');

       }
    }

?>
