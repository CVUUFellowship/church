<?php
    class Application_Form_DataPersonEntry extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datapersonentryForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datapersonentry');
          
          $date = new Zend_Form_Element_Hidden('date');

          $hid = new Zend_Form_Element_Text('hid');
          $hid->setLabel('Household ID');
          $hid->setAttrib('size', '4');

          $dummy = new Zend_Form_Element_hidden('dummy');

          $household = new Zend_Form_Element_Text('household');
          $household->setLabel('Household');
          $household->setAttrib('size', '50');

          $street = new Zend_Form_Element_Text('street');
          $street->setLabel('Street');
          $street->setAttrib('size', '20');

          $city = new Zend_Form_Element_Text('city');
          $city->setLabel('City');
          $city->setAttrib('size', '20');

          $state = new Zend_Form_Element_Text('state');
          $state->setLabel('State');
          $state->setAttrib('size', '2');

          $zip = new Zend_Form_Element_Text('zip');
          $zip->setLabel('Zip');
          $zip->setAttrib('size', '10');

          $area = new Zend_Form_Element_Text('area');
          $area->setLabel('Area code');
          $area->setAttrib('size', '3');

          $phone = new Zend_Form_Element_Text('phone');
          $phone->setLabel('Phone');
          $phone->setAttrib('size', '8');


          $dummy3 = new Zend_Form_Element_hidden('dummy3');
          $dummy4 = new Zend_Form_Element_hidden('dummy4');
          $dummy5 = new Zend_Form_Element_hidden('dummy5');
          $dummy6 = new Zend_Form_Element_hidden('dummy6');

          $name = new Zend_Form_Element_Text('name');
          $name->setLabel('Name');
          $name->setAttrib('size', '40');

          $email = new Zend_Form_Element_Text('email');
          $email->setLabel('Email');
          $email->setAttrib('size', '50');

          $comments = new Zend_Form_Element_Text('comments');
          $comments->setLabel('Comments');
          $comments->setAttrib('size', '40');

          $status = new Zend_Form_Element_Select('status');
          $status->setLabel('Status');
          $status->addMultiOptions(array());
          $status->setSeparator('');                 

          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('COMMIT');

          $xbutton = new Zend_Form_Element_Submit('xbutton');
          $xbutton->setLabel('CLEAR');
             
          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');


          $this->addElements(array($hidden,
              $date, $hid, $dummy, 
              $household, $street, $city,
              $state, $zip, $area, $phone, 
              $dummy3, $name, $email, $comments, 
              $status, $dummy2, $ebutton, 
              $dummy, $xbutton, $cbutton,
              ));

          $this->setMethod('post');

       }
    }

?>
