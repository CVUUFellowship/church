<?php
    class Application_Form_REEnroll2a extends Application_Form_REEnroll2 {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenroll2aForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenroll2aForm');

          $abutton = new Zend_Form_Element_Submit('abutton');
          $abutton->setLabel('CHANGE');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $hidden, $abutton, 
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
