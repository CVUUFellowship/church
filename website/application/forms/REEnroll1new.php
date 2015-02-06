<?php
    class Application_Form_REEnroll1new extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenroll1newForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenroll1newForm');

          $dummy = new Zend_Form_Element_hidden('dummy');
          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $firstname = new Zend_Form_Element_Text('firstname');
          $firstname->setLabel('Child First Name');
          $firstname->setAttrib('size', '15');

          $lastname = new Zend_Form_Element_Text('lastname');
          $lastname->setLabel('Child Last Name');
          $lastname->setAttrib('size', '30');

          $birth = new Zend_Form_Element_Text('birth');
          $birth->setLabel('Date of Birth');
          $birth->setAttrib('size', '20');

          $grade = new Zend_Form_Element_Text('grade');
          $grade->setLabel('Current Grade');
          $grade->setAttrib('size', '10');

          $gender = new Zend_Form_Element_Radio('gender');
          $gender->setLabel('Gender');
          $gender->addMultiOptions(array(
                    'F' => 'F',
                    'M' => 'M', 
                    ))
              ->setSeparator('');                 

             
          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('CLEAR');
             
          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $firstname, $lastname, $birth, $grade, $gender,
              $dummy2, $cbutton, $ebutton, 
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
