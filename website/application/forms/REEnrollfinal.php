<?php
    class Application_Form_REEnrollfinal extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenrollfinalForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenrollfinalForm');
             
          $zbutton = new Zend_Form_Element_Submit('zbutton');
          $zbutton->setLabel('FINAL');

          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $hidden, $zbutton,
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
