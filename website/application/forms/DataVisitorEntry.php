<?php
    class Application_Form_DataVisitorEntry extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datavisitorentryForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datavisitorentry');

          $date = new Zend_Form_Element_Text('date');
          $date->setLabel('Visit date');
          $date->setAttrib('size', '10');

          $hid = new Zend_Form_Element_Text('hid');
          $hid->setLabel('Household ID');
          $hid->setAttrib('size', '4');

          $dummy = new Zend_Form_Element_hidden('dummy');

          $household = new Zend_Form_Element_Text('household');
          $household->setLabel('Household');
          $household->setAttrib('size', '50');

          $street = new Zend_Form_Element_Text('street');
          $street->setLabel('Street');
          $street->setAttrib('size', '50');

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

          $name2 = new Zend_Form_Element_Text('name2');
          $name2->setLabel('Name');
          $name2->setAttrib('size', '40');

          $email = new Zend_Form_Element_Text('email');
          $email->setLabel('Email');
          $email->setAttrib('size', '50');

          $email2 = new Zend_Form_Element_Text('email2');
          $email2->setLabel('Email');
          $email2->setAttrib('size', '50');

          $comments = new Zend_Form_Element_Text('comments');
          $comments->setLabel('Comments');
          $comments->setAttrib('size', '40');

          $comments2 = new Zend_Form_Element_Text('comments2');
          $comments2->setLabel('Comments');
          $comments2->setAttrib('size', '40');

          $prioruu = new Zend_Form_Element_Radio('prioruu');
          $prioruu->setLabel('Prior UU');
          $prioruu->addMultiOptions(array(
                    'yes' => 'yes',
                    'no' => 'no', 
                    ))
              ->setSeparator('');                 


          $prioruu2 = new Zend_Form_Element_Radio('prioruu2');
          $prioruu2->setLabel('Prior UU');
          $prioruu2->addMultiOptions(array(
                    'yes' => 'yes',
                    'no' => 'no', 
                    ))
              ->setSeparator('');                 

          $refer = new Zend_Form_Element_Select('refer');
          $refer->setLabel('Referred by');
          $refer->addMultiOptions(array(
                    'none' => 'none',
                    'friend' => 'friend', 
                    'web' => 'web', 
                    'paper' => 'paper', 
                    'other' => 'other',
                    ));
          $refer->setSeparator('');                 


          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('COMMIT');

          $xbutton = new Zend_Form_Element_Submit('xbutton');
          $xbutton->setLabel('CLEAR');
             
          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');


          $this->addElements(array($hidden,
              $date, $hidden, $dummy6, $hid, $dummy, 
              $household, $street, $city,
              $state, $zip, $area, $phone, 
              $dummy3, $name, $email, $comments, $prioruu, 
              $dummy4, $name2, $email2, $comments2, $prioruu2,
              $dummy5, $refer, $dummy2, $ebutton, 
              $dummy, $xbutton, $cbutton,
              ));

          $this->setMethod('post');

       }
    }

?>
