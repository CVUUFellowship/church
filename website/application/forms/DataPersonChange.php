<?php
    class Application_Form_DataPersonChange extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datapersonchangeForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datapersonchange');

          $date = new Zend_Form_Element_hidden('date');

          $hid = new Zend_Form_Element_Text('hid');
          $hid->setLabel('Household ID');
          $hid->setAttrib('size', '4');

          $householdid = new Zend_Form_Element_hidden('householdid');

          $dummy = new Zend_Form_Element_hidden('dummy');

          $household = new Zend_Form_Element_Text('householdname');
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

          $hood = new Zend_Form_Element_Select('hood');
          $hood->setLabel('Neighborhood');
          $hood->addMultiOptions(array());
          $hood->setSeparator('');                 

          $phone = new Zend_Form_Element_Text('phone');
          $phone->setLabel('Home phone');
          $phone->setAttrib('size', '12');


          $dummy3 = new Zend_Form_Element_hidden('dummy3');
          $dummy4 = new Zend_Form_Element_hidden('dummy4');
          $dummy5 = new Zend_Form_Element_hidden('dummy5');
          $dummy6 = new Zend_Form_Element_hidden('dummy6');

          $firstname = new Zend_Form_Element_Text('firstname');
          $firstname->setLabel('First name');
          $firstname->setAttrib('size', '40');

          $lastname = new Zend_Form_Element_Text('lastname');
          $lastname->setLabel('Last name');
          $lastname->setAttrib('size', '40');

          $email = new Zend_Form_Element_Text('email');
          $email->setLabel('Email');
          $email->setAttrib('size', '50');

          $persphone = new Zend_Form_Element_Text('pphone');
          $persphone->setLabel('Personal phone');
          $persphone->setAttrib('size', '12');

          $comments = new Zend_Form_Element_Text('comments');
          $comments->setLabel('Comments');
          $comments->setAttrib('size', '40');

          $photo = new Zend_Form_Element_Text('photolink');
          $photo->setLabel('Photo');
          $photo->setAttrib('size', '40');

          $inactive = new Zend_Form_Element_Radio('inactive');
          $inactive->setLabel('Inactive');
          $inactive->addMultiOptions(array(
                    'yes' => 'yes',
                    'no' => 'no', 
                    ))
              ->setSeparator('');                 

          $status = new Zend_Form_Element_Select('status');
          $status->setLabel('Status');
          $status->addMultiOptions(array());
          $status->setSeparator('');                 

          $frienddate = new Zend_Form_Element_Text('frienddate');
          $frienddate->setLabel('Friend date');
          $frienddate->setAttrib('size', '10');

          $memberdate = new Zend_Form_Element_Text('membershipdate');
          $memberdate->setLabel('Membership date');
          $memberdate->setAttrib('size', '10');

          $inducted = new Zend_Form_Element_Radio('inducted');
          $inducted->setLabel('Inducted');
          $inducted->addMultiOptions(array(
                    'Yes' => 'Yes',
                    'No' => 'No', 
                    ))
              ->setSeparator('');                 

          $angelid = new Zend_Form_Element_Text('angelid');
          $angelid->setLabel('Angel ID');
          $angelid->setAttrib('size', '5');

          $emailnewsletter = new Zend_Form_Element_Radio('emailnewsletter');
          $emailnewsletter->setLabel('Email Newsletter');
          $emailnewsletter->addMultiOptions(array(
                    'yes' => 'yes',
                    'no' => 'no', 
                    ))
              ->setSeparator('');                 

          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('COMMIT');

          $xbutton = new Zend_Form_Element_Submit('xbutton');
          $xbutton->setLabel('CLEAR');
             
          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');

          
          $pid = new Zend_Form_Element_hidden('pid');
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array($hidden,
              $date, $hidden, $dummy6, $hid, $householdid, $dummy, 
              $household, $street, $city,
              $state, $zip, $hood, $phone,
              $dummy3, $firstname, $lastname, $email, $persphone, 
              $comments, $photo, $dummy4, $inactive, $status,
              $frienddate, $memberdate, $inducted, $angelid,
              $emailnewsletter,  
              $dummy2, $ebutton, 
              $dummy, $xbutton, $cbutton,
              $pid, $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
