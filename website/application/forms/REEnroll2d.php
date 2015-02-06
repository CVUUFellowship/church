<?php
    class Application_Form_REEnroll2d extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $dummy = new Zend_Form_Element_hidden('dummy');
          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $householdid = new Zend_Form_Element_hidden('householdid');

          $dummy = new Zend_Form_Element_hidden('dummy');

          $street = new Zend_Form_Element_Text('street');
          $street->setLabel('Street');
          $street->setAttrib('size', '50');
          $street->setAttrib('disabled', true);
          
          $city = new Zend_Form_Element_Text('city');
          $city->setLabel('City');
          $city->setAttrib('size', '20');
          $city->setAttrib('disabled', true);

          $state = new Zend_Form_Element_Text('state');
          $state->setLabel('State');
          $state->setAttrib('size', '2');
          $state->setAttrib('disabled', true);

          $zip = new Zend_Form_Element_Text('zip');
          $zip->setLabel('Zip');
          $zip->setAttrib('size', '10');
          $zip->setAttrib('disabled', true);

          $phone = new Zend_Form_Element_Text('phone');
          $phone->setLabel('Home phone');
          $phone->setAttrib('size', '12');

          $email = new Zend_Form_Element_Text('email');
          $email->setLabel('Email');
          $email->setAttrib('size', '50');

          $persphone = new Zend_Form_Element_Text('pphone');
          $persphone->setLabel('Personal phone');
          $persphone->setAttrib('size', '12');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $street, $city,
              $state, $zip, $phone, $dummy2, 
              $email, $persphone,
              $keys, $values, 
              ));

          $this->setMethod('post');

       }
    }

?>
