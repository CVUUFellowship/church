<?php
    class Application_Form_REEnroll2p extends Application_Form_REEnroll2 {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenroll2pForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenroll2pForm');

          $pbutton = new Zend_Form_Element_Submit('pbutton');
          $pbutton->setLabel('CHANGE');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $hidden, $pbutton, 
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
