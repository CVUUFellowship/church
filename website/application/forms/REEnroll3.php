<?php
    class Application_Form_REEnroll3 extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenroll3Form');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenroll3Form');

          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('CHANGE');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $hidden, $cbutton, 
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
