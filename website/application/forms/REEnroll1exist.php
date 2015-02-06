<?php
    class Application_Form_REEnroll1exist extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenroll1existForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenroll1existForm');

          $dummy = new Zend_Form_Element_hidden('dummy');

          $firstname = new Zend_Form_Element_Text('firstname');
          $firstname->setLabel('Child First Name Begins');
          $firstname->setAttrib('size', '10');

          $lastname = new Zend_Form_Element_Text('lastname');
          $lastname->setLabel('Child Last Name Begins');
          $lastname->setAttrib('size', '10');

          $recid = new Zend_Form_Element_Text('recid');
          $recid->setLabel('Registration number');
          $recid->setAttrib('size', '4');
             
          $fbutton = new Zend_Form_Element_Submit('fbutton');
          $fbutton->setLabel('FIND');

          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $firstname, $lastname, $dummy, $recid,
              $hidden, $fbutton, 
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
